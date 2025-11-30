<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookController; 
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservationController;
use App\Models\Loan;
use App\Models\Book;

Route::get('/', function (Request $request) {
    $query = Book::query();
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

Route::get('/books', [BookController::class, 'index'])->name('books.index');


Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/pegawai/dashboard', function () {
        $loans = Loan::with(['user', 'book'])->where('status', 'borrowed')->get();        
        return view('pegawai.dashboard', compact('loans'));
    })->name('pegawai.dashboard');

    Route::get('/pegawai/trigger-reminders', function () {
        \Illuminate\Support\Facades\Artisan::call('pustaka:send-reminders');
        return back()->with('success', '✅ Notifikasi Pengingat berhasil dikirim ke semua mahasiswa!');
    })->name('pegawai.trigger.reminders');

    Route::get('/pegawai/notifications-log', function () {
        $logs = \Illuminate\Support\Facades\DB::table('notifications')
                ->orderBy('created_at', 'desc')
                ->paginate(20);
        return view('pegawai.notifications', compact('logs'));
    })->name('pegawai.notifications');
});

Route::middleware(['auth', 'role:mahasiswa'])->group(function () {
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $activeLoans = Loan::with('book')->where('user_id', $user->id)->where('status', 'borrowed')->get();
        $historyLoans = Loan::with('book')->where('user_id', $user->id)->where('status', 'returned')->orderBy('updated_at', 'desc')->get();

        $notifications = $user->notifications()->latest()->take(5)->get();

        $reservations = \App\Models\Reservation::with('book')
                        ->where('user_id', $user->id)
                        ->where('status', 'active')
                        ->orderBy('created_at', 'desc')
                        ->get();

        $lastLoan = Loan::with('book')->where('user_id', $user->id)->latest()->first();
        if ($lastLoan) {
            $category = $lastLoan->book->category;
            $recommendations = Book::where('category', $category)
                                ->where('id', '!=', $lastLoan->book_id) 
                                ->inRandomOrder()->limit(3)->get();
        } else {
            $recommendations = Book::latest()->limit(3)->get();
        }

        return view('dashboard', compact('activeLoans', 'historyLoans', 'notifications', 'recommendations', 'reservations'));
    })->name('dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('books', BookController::class)->except(['index', 'show']); 

    Route::post('/loans', [LoanController::class, 'store'])->name('loans.store');
    Route::post('/loans/{id}/renew', [LoanController::class, 'renew'])->name('loans.renew');
    Route::post('/loans/{id}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::post('/loans/{id}/pay', [LoanController::class, 'payFine'])->name('loans.pay');
    Route::post('/books/{id}/review', [BookController::class, 'storeReview'])->name('books.review');
    Route::get('/notifications', [ProfileController::class, 'notifications'])->name('notifications.index');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

require __DIR__.'/auth.php';