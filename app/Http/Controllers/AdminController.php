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
        $totalBooks = Book::count(); 
        $totalStock = Book::sum('stock'); 
        
        $activeLoans = Loan::where('status', 'borrowed')->count(); 
        $returnedLoans = Loan::where('status', 'returned')->count(); 
        
        $totalFinesPaid = Loan::where('payment_status', 'paid')->sum('fine_amount');
        $totalFinesPending = Loan::where('payment_status', 'pending')->sum('fine_amount');

        $popularBooks = Book::withCount('loans')
                            ->orderBy('loans_count', 'desc')
                            ->take(5)
                            ->get();

        $chartData = Book::select('category', DB::raw('count(*) as total'))
                         ->join('loans', 'books.id', '=', 'loans.book_id') 
                         ->groupBy('category')
                         ->pluck('total', 'category'); 

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