<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    // Agar Mahasiswa tidak bisa akses halaman ini (Security Guard)
    public function __construct()
    {
        // Hanya user yg login bisa akses
        // Nanti kita tambah cek role di logic kalau perlu
    }

    /**
     * Menampilkan daftar buku.
     */
    public function index()
    {
        $books = Book::all();
        return view('books.index', compact('books'));
    }

    /**
     * Menampilkan form tambah buku.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Menyimpan buku baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'publication_year' => 'required|integer|min:1900|max:'.(date('Y')+1),
            'category' => 'required|string',
            'stock' => 'required|integer|min:0',
            'max_loan_days' => 'integer|min:1',
            'fine_per_day' => 'numeric|min:0',
        ]);

        Book::create($request->all());

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Menampilkan Detail Buku + Review
     */
    public function show($id)
    {
        // Ambil buku beserta review dan user yang me-review
        $book = Book::with(['reviews.user'])->findOrFail($id);
        
        // Cek apakah user yang sedang login sudah pernah pinjam buku ini?
        // (Syarat: Cuma boleh review kalau sudah pernah pinjam & statusnya 'returned')
        $canReview = false;
        
        if (Auth::check() && Auth::user()->role == 'mahasiswa') {
            $hasBorrowed = \App\Models\Loan::where('user_id', Auth::id())
                            ->where('book_id', $book->id)
                            ->where('status', 'returned')
                            ->exists();
            
            // Cek juga apakah dia SUDAH pernah review (biar ga spam review berkali-kali)
            $alreadyReviewed = Review::where('user_id', Auth::id())
                                     ->where('book_id', $book->id)
                                     ->exists();
                                     
            if ($hasBorrowed && !$alreadyReviewed) {
                $canReview = true;
            }
        }

        return view('books.show', compact('book', 'canReview'));
    }

    /**
     * Simpan Review Baru
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
