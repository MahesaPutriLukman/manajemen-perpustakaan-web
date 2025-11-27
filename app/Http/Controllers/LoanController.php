<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // Wajib import ini buat mainan tanggal

class LoanController extends Controller
{
    /**
     * LOGIKA 1: MEMINJAM BUKU (Dipakai Mahasiswa)
     */
    public function store(Request $request)
    {
        // 1. Validasi: Pastikan buku & user valid
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::findOrFail($request->book_id);
        $user = Auth::user();

        // 2. Cek Stok Buku
        if ($book->stock < 1) {
            return back()->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        // 3. Cek Apakah Mahasiswa Punya Denda Tertunggak? (Sesuai Soal)
        // Kita cek apakah ada pinjaman dia yang statusnya 'returned' tapi dendanya belum lunas (opsional, tahap lanjut)
        // Untuk sekarang kita cek apakah dia sedang minjam buku yang sama
        $isBorrowing = Loan::where('user_id', $user->id)
                            ->where('book_id', $book->id)
                            ->where('status', 'borrowed')
                            ->exists();

        if ($isBorrowing) {
            return back()->with('error', 'Anda sedang meminjam buku ini. Kembalikan dulu sebelum pinjam lagi.');
        }

        // 4. Proses Peminjaman
        // Kurangi Stok
        $book->decrement('stock');

        // Catat di Tabel Loans
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => Carbon::now(), // Hari ini
            'due_date' => Carbon::now()->addDays($book->max_loan_days), // Jatuh tempo sesuai aturan buku
            'status' => 'borrowed',
        ]);

        return redirect()->route('books.index')->with('success', 'Berhasil meminjam buku! Jangan lupa kembalikan tepat waktu.');
    }

    /**
     * LOGIKA 2: MENGEMBALIKAN BUKU (Dipakai Pegawai/Admin)
     */
    public function returnBook($loan_id)
    {
        $loan = Loan::findOrFail($loan_id);
        $book = $loan->book;

        // Cek apakah sudah dikembalikan sebelumnya
        if ($loan->status == 'returned') {
            return back()->with('error', 'Buku ini sudah dikembalikan.');
        }

        // 1. Hitung Denda
        $today = Carbon::now();
        $dueDate = Carbon::parse($loan->due_date);
        
        $fineAmount = 0;
        
        // Kalau hari ini lebih besar dari jatuh tempo, berarti telat
        if ($today->gt($dueDate)) {
            $lateDays = $today->diffInDays($dueDate); // Selisih hari
            $fineAmount = $lateDays * $book->fine_per_day; // Hari x Denda per hari
        }

        // 2. Update Data Peminjaman
        $loan->update([
            'return_date' => $today,
            'status' => 'returned',
            'fine_amount' => $fineAmount,
        ]);

        // 3. Kembalikan Stok Buku
        $book->increment('stock');

        // 4. Pesan Balikan
        if ($fineAmount > 0) {
            return back()->with('warning', 'Buku dikembalikan TERLAMBAT. Denda: Rp ' . number_format($fineAmount, 0, ',', '.'));
        }

        return back()->with('success', 'Buku berhasil dikembalikan. Terima kasih!');
    }
}