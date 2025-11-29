<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // 1. STATISTIK KARTU (Cards)
        $totalBooks = Book::count(); // Jumlah Judul Buku
        $totalStock = Book::sum('stock'); // Total Eksemplar Buku
        
        $activeLoans = Loan::where('status', 'borrowed')->count(); // Sedang Dipinjam
        $returnedLoans = Loan::where('status', 'returned')->count(); // Sudah Kembali
        
        // Hitung Denda: Yang sudah dibayar vs Potensi (Pending)
        $totalFinesPaid = Loan::where('payment_status', 'paid')->sum('fine_amount');
        $totalFinesPending = Loan::where('payment_status', 'pending')->sum('fine_amount');

        // 2. STATISTIK BUKU TERPOPULER (Top 5)
        // Menggunakan Eloquent untuk menghitung buku mana yang paling sering ada di tabel loans
        $popularBooks = Book::withCount('loans')
                            ->orderBy('loans_count', 'desc')
                            ->take(5)
                            ->get();

        // 3. DATA UNTUK GRAFIK (Peminjaman per Kategori)
        // Kita hitung berapa kali buku dipinjam berdasarkan kategorinya
        $chartData = Book::select('category', DB::raw('count(*) as total'))
                         ->join('loans', 'books.id', '=', 'loans.book_id') // Join ke tabel loans
                         ->groupBy('category')
                         ->pluck('total', 'category'); // Hasil: ['Fiksi' => 10, 'Sains' => 5]

        return view('admin.dashboard', compact(
            'totalBooks', 
            'totalStock', 
            'activeLoans', 
            'returnedLoans', 
            'totalFinesPaid', 
            'totalFinesPending',
            'popularBooks',
            'chartData'
        ));
    }
}