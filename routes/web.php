<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // <--- INI PENTING (Baru Ditambah)
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController; 
use App\Http\Controllers\LoanController; // Biar rapi pakai ini juga
use App\Http\Controllers\UserController; // Biar rapi
use App\Models\Loan;
use App\Models\Book;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- HALAMAN DEPAN (GUEST) ---
Route::get('/', function (Request $request) {
    $query = Book::query();

    // Logika Search
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

// --- GROUP ROUTE ADMIN ---
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); 
    })->name('admin.dashboard');

    // CRUD User Management
    Route::resource('users', UserController::class);
});

// --- GROUP ROUTE PEGAWAI ---
Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/dashboard', function () {
        // Ambil peminjaman yang sedang aktif (borrowed)
        $loans = Loan::with(['user', 'book'])
                     ->where('status', 'borrowed')
                     ->get();        
        return view('pegawai.dashboard', compact('loans'));
    })->name('pegawai.dashboard');
    Route::get('/pegawai/trigger-reminders', function () {
        // Panggil command robot
        \Illuminate\Support\Facades\Artisan::call('pustaka:send-reminders');
        
        // Kembali dengan pesan sukses yang JELAS
        return back()->with('success', '✅ Notifikasi Pengingat berhasil dikirim ke semua mahasiswa!');
    })->name('pegawai.trigger.reminders');
});

// --- GROUP ROUTE MAHASISWA ---
Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        // 1. Data Pinjaman (Aktif & Riwayat)
        $activeLoans = Loan::with('book')->where('user_id', $user->id)->where('status', 'borrowed')->get();
        $historyLoans = Loan::with('book')->where('user_id', $user->id)->where('status', 'returned')->orderBy('updated_at', 'desc')->get();

        // 2. Data Notifikasi (Ambil 5 terbaru)
        $notifications = $user->notifications()->latest()->take(5)->get();

        // 3. LOGIKA REKOMENDASI CERDAS
        // Cek buku apa yang terakhir dipinjam user
        $lastLoan = Loan::with('book')->where('user_id', $user->id)->latest()->first();
        
        if ($lastLoan) {
            // Kalau pernah pinjam, cari buku lain yang KATEGORINYA SAMA
            // Tapi jangan tampilkan buku yang sedang dipinjam sekarang
            $category = $lastLoan->book->category;
            $recommendations = Book::where('category', $category)
                                ->where('id', '!=', $lastLoan->book_id) 
                                ->inRandomOrder()
                                ->limit(3)
                                ->get();
        } else {
            // Kalau user baru (belum pernah pinjam), tampilkan buku Random/Terbaru
            $recommendations = Book::latest()->limit(3)->get();
        }

        return view('dashboard', compact('activeLoans', 'historyLoans', 'notifications', 'recommendations'));
    })->name('dashboard');
});

// --- ROUTE UMUM (LOGIN REQUIRED) ---
Route::middleware('auth')->group(function () {
    // Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Manajemen Buku (CRUD)
    Route::resource('books', BookController::class); 

    // Manajemen Peminjaman & Denda
    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{id}/renew', [LoanController::class, 'renew'])->name('loans.renew');
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/loans/{id}/pay', [LoanController::class, 'payFine'])->name('loans.pay');

    // Kirim Review
    Route::post('/books/{id}/review', [BookController::class, 'storeReview'])->name('books.review');
    // Route Riwayat Notifikasi Lengkap
    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications.index');
});

require __DIR__.'/auth.php';