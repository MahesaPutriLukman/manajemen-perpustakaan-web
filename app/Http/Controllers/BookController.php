<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Notifications\GeneralNotification;

class BookController extends Controller
{
    public function __construct()
    {
        // Security Guard
    }

    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest': $query->orderBy('created_at', 'desc'); break;
                case 'year_desc': $query->orderBy('publication_year', 'desc'); break;
                case 'year_asc': $query->orderBy('publication_year', 'asc'); break;
                case 'title_asc': $query->orderBy('title', 'asc'); break;
                case 'stock_desc': $query->orderBy('stock', 'desc'); break;
                default: $query->latest();
            }
        } else {
            $query->latest();
        }

        $books = $query->get();
        $categories = Book::select('category')->distinct()->pluck('category');

        return view('books.index', compact('books', 'categories'));
    }

    public function create()
    {
        return view('books.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1000|max:'.(date('Y')+1),
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'max_loan_days' => 'integer|min:1',
            'fine_per_day' => 'numeric|min:0',
        ]);

        Book::create($request->all());

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show($id)
    {
        $book = Book::with(['reviews.user'])->findOrFail($id);
        $canReview = false;
        
        if (Auth::check() && Auth::user()->role == 'mahasiswa') {
            $hasBorrowed = \App\Models\Loan::where('user_id', Auth::id())
                            ->where('book_id', $book->id)
                            ->where('status', 'returned')
                            ->exists();
            
            $alreadyReviewed = Review::where('user_id', Auth::id())
                                     ->where('book_id', $book->id)
                                     ->exists();
                                     
            if ($hasBorrowed && !$alreadyReviewed) {
                $canReview = true;
            }
        }

        return view('books.show', compact('book', 'canReview'));
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        return view('books.edit', compact('book'));
    }

    /**
     * FUNGSI UPDATE 
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|max:'.(date('Y')+1),
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'max_loan_days' => 'integer|min:1',
            'fine_per_day' => 'numeric|min:0',
        ]);

        $book = Book::findOrFail($id);
        
        $oldStock = $book->stock;

        $book->update($request->all());

        if ($oldStock <= 0 && $book->stock > 0) {
            $reservations = Reservation::with('user')
                                       ->where('book_id', $book->id)
                                       ->where('status', 'active')
                                       ->get();

            foreach ($reservations as $reservation) {
                $reservation->user->notify(new GeneralNotification(
                    "KABAR GEMBIRA! Buku '" . $book->title . "' yang kamu reservasi STOKNYA SUDAH TERSEDIA. Segera pinjam!"
                ));
                $reservation->update(['status' => 'fulfilled']);
            }
        }

        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        $isBorrowed = $book->loans()->where('status', 'borrowed')->exists();
        if ($isBorrowed) {
            return back()->with('error', 'GAGAL HAPUS! Buku ini sedang dipinjam oleh mahasiswa.');
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus dari koleksi.');
    }

    public function storeReview(Request $request, $book_id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $book_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Terima kasih atas ulasanmu!');
    }
}