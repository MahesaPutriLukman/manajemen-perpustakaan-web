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
    public function index(Request $request)
    {
        // Mulai Query
        $query = Book::query();

        // 1. Logic Search (Pencarian)
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('author', 'like', '%' . $search . '%');
            });
        }

        // 2. Logic Filter Kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // 3. Logic Sorting (Pengurutan)
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc'); // Terbaru diinput
                    break;
                case 'year_desc':
                    $query->orderBy('publication_year', 'desc'); // Tahun Terbit Terbaru
                    break;
                case 'year_asc':
                    $query->orderBy('publication_year', 'asc'); // Tahun Terbit Terlama
                    break;
                case 'title_asc':
                    $query->orderBy('title', 'asc'); // Judul A-Z
                    break;
                case 'stock_desc':
                    $query->orderBy('stock', 'desc'); // Stok Terbanyak
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest(); // Default
        }

        $books = $query->get();
        
        // Ambil daftar kategori unik untuk dropdown filter
        $categories = Book::select('category')->distinct()->pluck('category');

        return view('books.index', compact('books', 'categories'));
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
            
            // UBAH DISINI: Ganti 1900 jadi 1000 (atau hapus min:1900 nya)
            'publication_year' => 'required|integer|max:'.(date('Y')+1),
            
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
     * Tampilkan Form Edit Buku
     */
    public function edit($id)
    {
        // Cari buku berdasarkan ID, kalau ga ketemu error 404
        $book = Book::findOrFail($id);
        
        // Tampilkan view edit yang barusan kita buat
        return view('books.edit', compact('book'));
    }

    /**
     * Proses Update Data Buku ke Database
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi Input (Mirip store, tapi tidak perlu cek unique)
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

        // 2. Ambil buku & Update
        $book = Book::findOrFail($id);
        
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'publication_year' => $request->publication_year,
            'category' => $request->category,
            'stock' => $request->stock,
            'max_loan_days' => $request->max_loan_days,
            'fine_per_day' => $request->fine_per_day,
        ]);

        // 3. Redirect ke halaman detail atau index
        return redirect()->route('books.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * Hapus Buku
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus dari koleksi.');
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
}