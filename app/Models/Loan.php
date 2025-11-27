<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    // Daftar kolom yang boleh diisi
    protected $fillable = [
        'user_id',
        'book_id',
        'loan_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
    ];

    // --- RELASI ANTAR TABEL (PENTING) ---

    // 1. Peminjaman ini milik siapa? (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 2. Buku apa yang dipinjam? (Book)
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}