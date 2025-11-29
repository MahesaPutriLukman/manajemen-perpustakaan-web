<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'comment',
    ];

    // Relasi: Review ini milik User siapa?
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Review ini untuk Buku apa?
    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}