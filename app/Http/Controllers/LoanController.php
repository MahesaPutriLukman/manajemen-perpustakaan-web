<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Notifications\GeneralNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * LOGIKA 1: MEMINJAM BUKU (Mahasiswa)
     * + Fitur Blokir jika ada denda tertunggak
     */
    public function store(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);
        $book = Book::findOrFail($request->book_id);
        $user = Auth::user();

        // CEK 1: Blokir jika stok habis
        if ($book->stock < 1) {
            return back()->with('error', 'Stok buku habis.');
        }

        // CEK 2: Blokir jika sedang meminjam buku yang sama
        $isBorrowing = Loan::where('user_id', $user->id)
                           ->where('book_id', $book->id)
                           ->where('status', 'borrowed')
                           ->exists();
                           
        if ($isBorrowing) {
            return back()->with('error', 'Anda sedang meminjam buku ini.');
        }

        // CEK 3: BLOKIR JIKA ADA DENDA BELUM LUNAS (History)
        $hasUnpaidFine = Loan::where('user_id', $user->id)
                             ->where('payment_status', 'pending')
                             ->exists();
                             
        if ($hasUnpaidFine) {
            return back()->with('error', 'ANDA DIBLOKIR! Harap lunasi denda sebelumnya di perpustakaan.');
        }

        // --- CEK 4 (BARU): BLOKIR JIKA ADA BUKU YANG SEDANG TELAT (Active) ---
        $hasOverdueItem = Loan::where('user_id', $user->id)
                              ->where('status', 'borrowed')
                              ->where('due_date', '<', Carbon::now()->startOfDay()) // Cek apakah jatuh tempo < hari ini
                              ->exists();

        if ($hasOverdueItem) {
            return back()->with('error', 'ANDA DIBLOKIR! Anda masih meminjam buku yang sudah lewat jatuh tempo. Harap kembalikan dulu.');
        }
        // ---------------------------------------------------------------------

        // PROSES PINJAM
        $book->decrement('stock');
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays($book->max_loan_days),
            'status' => 'borrowed',
            'payment_status' => 'no_fine', 
        ]);

        // Kirim Notifikasi
        $user->notify(new GeneralNotification("Anda berhasil meminjam buku: " . $book->title . ". Harap kembalikan sebelum " . $book->max_loan_days . " hari."));

        return redirect()->route('dashboard')->with('success', 'Berhasil meminjam buku!');
    }

    /**
     * LOGIKA 2: KEMBALIKAN BUKU (Pegawai)
     * + Hitung Denda Otomatis (FIXED: StartOfDay & Abs)
     */
    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        $book = $loan->book;

        if ($loan->status == 'returned') return back();

        // 1. Reset jam
        $today = Carbon::now()->startOfDay(); 
        $dueDate = Carbon::parse($loan->due_date)->startOfDay();
        
        $fineAmount = 0;
        $paymentStatus = 'no_fine';

        // 2. Hitung Denda
        if ($today->gt($dueDate)) {
            $lateDays = abs($today->diffInDays($dueDate));
            $fineAmount = $lateDays * $book->fine_per_day;
            $paymentStatus = 'pending'; 
        }

        // 3. Update Database Loan
        $loan->update([
            'return_date' => Carbon::now(),
            'status' => 'returned',
            'fine_amount' => $fineAmount,
            'payment_status' => $paymentStatus
        ]);

        // 4. Kembalikan Stok
        $book->increment('stock');

        // --- LOGIKA BARU: CEK RESERVASI ---
        // Cari siapa yang sedang reservasi buku ini (status active)
        $reservations = \App\Models\Reservation::where('book_id', $book->id)
                                               ->where('status', 'active')
                                               ->get();

        foreach ($reservations as $reservation) {
            // Kirim Notifikasi ke pemesan
            $reservation->user->notify(new GeneralNotification(
                "HORE! Buku '" . $book->title . "' yang kamu reservasi sudah tersedia. Segera pinjam sebelum kehabisan!"
            ));
            
            // Opsional: Tandai reservasi selesai (fulfilled) agar tidak dinotif terus
            // Atau biarkan active sampai dia beneran pinjam. 
            // Kita ubah ke fulfilled supaya antrean dianggap 'sudah dipanggil'
            $reservation->update(['status' => 'fulfilled']);
        }
        // ----------------------------------

        // 5. Notifikasi Denda / Sukses ke Peminjam Awal
        if ($fineAmount > 0) {
            $loan->user->notify(new GeneralNotification(
                "Buku '" . $book->title . "' dikembalikan Terlambat $lateDays hari. Denda: Rp " . number_format($fineAmount)
            ));
            return back()->with('warning', 'Buku telat ' . $lateDays . ' hari. Denda: Rp ' . number_format($fineAmount));
        }

        $loan->user->notify(new GeneralNotification("Buku '" . $book->title . "' telah dikembalikan. Terima kasih!"));

        return back()->with('success', 'Buku dikembalikan tepat waktu.');
    }
    

    /**
     * LOGIKA 3: PERPANJANG BUKU / RENEW (Mahasiswa)
     */
    public function renew($id)
    {
        $loan = Loan::findOrFail($id);

        // Validasi: Cuma boleh diperpanjang kalau belum telat
        if (Carbon::now()->gt($loan->due_date)) {
            return back()->with('error', 'Gagal perpanjang! Buku sudah lewat jatuh tempo.');
        }

        // Tambah 3 Hari
        $newDueDate = Carbon::parse($loan->due_date)->addDays(3);
        $loan->update(['due_date' => $newDueDate]);

        // Kirim Notifikasi
        $message = "Perpanjangan Berhasil! Buku '" . $loan->book->title . "' diperpanjang sampai tanggal " . $newDueDate->format('d-m-Y') . ".";
        $loan->user->notify(new GeneralNotification($message));

        return back()->with('success', 'Berhasil diperpanjang 3 hari!');
    }

    /**
     * LOGIKA 4: BAYAR DENDA (Pegawai)
     */
    public function payFine($id)
    {
        $loan = Loan::findOrFail($id);
        $loan->update(['payment_status' => 'paid']); // Tandai Lunas

        return back()->with('success', 'Denda telah dibayar Lunas.');
    }
}