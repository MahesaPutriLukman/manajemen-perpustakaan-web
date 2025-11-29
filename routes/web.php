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

    Route::resource('users', \App\Http\Controllers\UserController::class);
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
    // --- PROFIL USER ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MANAJEMEN BUKU ---
    Route::resource('books', BookController::class); 

    // --- MANAJEMEN PEMINJAMAN & DENDA (Complete) ---
    
    // 1. Proses Pinjam (Mahasiswa)
    Route::post('/loans', [App\Http\Controllers\LoanController::class, 'store'])->name('loans.store');

    // 2. Proses Perpanjang / Renew (Mahasiswa) -> INI BARU
    Route::post('/loans/{id}/renew', [App\Http\Controllers\LoanController::class, 'renew'])->name('loans.renew');

    // 3. Proses Kembali (Pegawai)
    Route::post('/loans/{id}/return', [App\Http\Controllers\LoanController::class, 'returnBook'])->name('loans.return');

    // 4. Proses Bayar Denda (Pegawai) -> INI BARU
    Route::post('/loans/{id}/pay', [App\Http\Controllers\LoanController::class, 'payFine'])->name('loans.pay');

    // Route Kirim Review
    Route::post('/books/{id}/review', [App\Http\Controllers\BookController::class, 'storeReview'])->name('books.review');
});

require __DIR__.'/auth.php';

require __DIR__.'/auth.php';