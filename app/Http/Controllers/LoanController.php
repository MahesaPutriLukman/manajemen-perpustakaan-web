<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
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
        $isBorrowing = Loan::where('user_id', $user->id)->where('book_id', $book->id)->where('status', 'borrowed')->exists();
        if ($isBorrowing) {
            return back()->with('error', 'Anda sedang meminjam buku ini.');
        }

        // CEK 3: BLOKIR JIKA ADA DENDA TERTUNGGAK (Sesuai PDF)
        $hasUnpaidFine = Loan::where('user_id', $user->id)
                             ->where('payment_status', 'pending') // Denda belum lunas
                             ->exists();
                             
        if ($hasUnpaidFine) {
            return back()->with('error', 'ANDA DIBLOKIR! Harap lunasi denda sebelumnya di perpustakaan.');
        }

        // PROSES PINJAM
        $book->decrement('stock');
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays($book->max_loan_days),
            'status' => 'borrowed',
            'payment_status' => 'no_fine', // Awal pinjam dianggap aman
        ]);

        return redirect()->route('dashboard')->with('success', 'Berhasil meminjam buku!');
    }

    /**
     * LOGIKA 2: KEMBALIKAN BUKU (Pegawai)
     * + Hitung Denda Otomatis
     */
    public function returnBook($id)
    {
        $loan = Loan::findOrFail($id);
        $book = $loan->book;

        if ($loan->status == 'returned') return back();

        $today = Carbon::now();
        $dueDate = Carbon::parse($loan->due_date);
        $fineAmount = 0;
        $paymentStatus = 'no_fine';

        // Hitung Denda
        if ($today->gt($dueDate)) {
            $lateDays = $today->diffInDays($dueDate);
            $fineAmount = $lateDays * $book->fine_per_day;
            $paymentStatus = 'pending'; // Tandai punya utang denda
        }

        $loan->update([
            'return_date' => $today,
            'status' => 'returned',
            'fine_amount' => $fineAmount,
            'payment_status' => $paymentStatus
        ]);

        $book->increment('stock');

        if ($fineAmount > 0) {
            return back()->with('warning', 'Buku telat! Denda: Rp ' . number_format($fineAmount));
        }

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