<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'publication_year',
        'category',
        'stock',
        'max_loan_days',
        'fine_per_day',
    ];

    // Relasi: Satu buku punya banyak review (Sudah ada sebelumnya)
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // --- TAMBAHAN BARU (INI YANG KURANG) ---
    // Relasi: Satu buku punya banyak data peminjaman (loans)
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}