<?php

use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController; 
use Illuminate\Support\Facades\Route;
use App\Models\Loan;

Route::get('/', function (Request $request) {
    $query = App\Models\Book::query();

    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('author', 'like', '%' . $search . '%')
              ->orWhere('category', 'like', '%' . $search . '%');
        });
    }

    $books = $query->latest()->get();

    return view('welcome', compact('books'));
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');
});

Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/dashboard', function () {
        $loans = Loan::with(['user', 'book'])
                     ->where('status', 'borrowed')
                     ->get();        
        return view('pegawai.dashboard', compact('loans'));
    })->name('pegawai.dashboard');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $activeLoans = App\Models\Loan::with('book')
                        ->where('user_id', $user->id)
                        ->where('status', 'borrowed')
                        ->get();

        $historyLoans = App\Models\Loan::with('book')
                        ->where('user_id', $user->id)
                        ->where('status', 'returned')
                        ->orderBy('updated_at', 'desc')
                        ->get();

        return view('dashboard', compact('activeLoans', 'historyLoans'));
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('books', BookController::class); 

    // 1. Route Proses Pinjam (Untuk tombol di index buku)
    Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');

    // 2. Route Proses Kembali (Nanti dipakai Pegawai)
    Route::post('/loans/{id}/return', [App\Http\Controllers\LoanController::class, 'returnBook'])->name('loans.return');
});

require __DIR__.'/auth.php';