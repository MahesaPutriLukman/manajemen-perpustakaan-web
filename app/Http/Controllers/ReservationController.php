<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);
        $book = Book::findOrFail($request->book_id);
        $user = Auth::user();

        // 1. Cek: Apakah stok beneran habis? (Validasi ganda)
        if ($book->stock > 0) {
            return back()->with('error', 'Buku masih tersedia! Silakan langsung pinjam, tidak perlu reservasi.');
        }

        // 2. Cek: Apakah user SUDAH pernah reservasi buku ini dan belum selesai?
        $existingReservation = Reservation::where('user_id', $user->id)
                                          ->where('book_id', $book->id)
                                          ->where('status', 'active')
                                          ->exists();

        if ($existingReservation) {
            return back()->with('error', 'Anda sudah masuk dalam antrean reservasi untuk buku ini.');
        }

        // 3. Buat Reservasi
        Reservation::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'active',
        ]);

        return back()->with('success', 'Berhasil reservasi! Kami akan memberitahu Anda saat buku tersedia.');
    }
}