<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative shadow-sm">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-sm animate-pulse">
                        <strong class="font-bold">⛔ Gagal!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        <div class="w-full h-64 bg-gradient-to-br from-blue-400 to-indigo-600 rounded-lg flex items-center justify-center mb-4 shadow-inner">
                            <span class="text-white text-4xl font-bold opacity-30">BOOK</span>
                        </div>
                        
                        <h3 class="text-xl font-bold mb-1">{{ $book->title }}</h3>
                        <p class="text-gray-500 mb-4">{{ $book->author }}</p>

                        <div class="mb-4">
                            @if($book->stock > 0)
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold">
                                    Stok Tersedia: {{ $book->stock }}
                                </span>
                            @else
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-bold">
                                    Stok Habis
                                </span>
                            @endif
                        </div>

                        @auth
                            @if(Auth::user()->role == 'mahasiswa')
                                
                                @if($book->stock > 0)
                                    <form action="{{ route('loans.store') }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="book_id" value="{{ $book->id }}">
                                        <button type="button" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition shadow btn-pinjam">
                                            Pinjam Sekarang
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-yellow-50 p-3 rounded border border-yellow-200">
                                        <p class="text-xs text-yellow-800 mb-2">Buku sedang kosong. Antre sekarang?</p>
                                        <form action="{{ route('reservations.store') }}" method="POST" class="w-full">
                                            @csrf
                                            <input type="hidden" name="book_id" value="{{ $book->id }}">
                                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded transition flex items-center justify-center shadow">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Reservasi (Antre)
                                            </button>
                                        </form>
                                    </div>
                                @endif

                            @elseif(Auth::user()->role == 'admin')
                                <a href="{{ route('books.edit', $book->id) }}" class="block w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-4 rounded text-center shadow">
                                    Edit Buku
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="block w-full bg-indigo-500 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded text-center shadow">
                                Login untuk Pinjam
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="md:col-span-2">
                    
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Informasi Detail</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 block">Penerbit</span>
                                <span class="font-semibold">{{ $book->publisher }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Tahun Terbit</span>
                                <span class="font-semibold">{{ $book->publication_year }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Kategori</span>
                                <span class="bg-gray-100 px-2 py-1 rounded text-xs font-semibold">{{ $book->category }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 block">Aturan Pinjam</span>
                                <span class="font-semibold text-red-500">Maks {{ $book->max_loan_days }} Hari</span>
                                <span class="text-xs text-gray-400">(Denda Rp {{ number_format($book->fine_per_day) }}/hari)</span>
                            </div>
                        </div>
                    </div>

                    @if(isset($canReview) && $canReview)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6 border-l-4 border-yellow-400">
                        <h3 class="text-lg font-bold mb-2">✍️ Tulis Ulasanmu</h3>
                        <p class="text-sm text-gray-600 mb-4">Bagaimana menurutmu buku ini?</p>

                        <form action="{{ route('books.review', $book->id) }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Rating</label>
                                <select name="rating" class="border rounded px-3 py-2 w-full md:w-1/3 focus:ring focus:ring-yellow-200">
                                    <option value="5">⭐⭐⭐⭐⭐ (Sangat Bagus)</option>
                                    <option value="4">⭐⭐⭐⭐ (Bagus)</option>
                                    <option value="3">⭐⭐⭐ (Cukup)</option>
                                    <option value="2">⭐⭐ (Kurang)</option>
                                    <option value="1">⭐ (Buruk)</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-bold mb-1">Komentar</label>
                                <textarea name="comment" rows="3" class="w-full border rounded px-3 py-2 focus:ring focus:ring-yellow-200" placeholder="Tulis pendapatmu..." required></textarea>
                            </div>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                    @endif

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold border-b pb-2 mb-4">Ulasan Pembaca ({{ $book->reviews->count() }})</h3>
                        
                        @forelse($book->reviews as $review)
                            <div class="mb-4 border-b pb-4 last:border-0 last:mb-0">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-gray-800">{{ $review->user->name }}</span>
                                    <span class="text-yellow-500 text-sm">
                                        @for($i=0; $i<$review->rating; $i++) ⭐ @endfor
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm italic">"{{ $review->comment }}"</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 py-4">Belum ada ulasan untuk buku ini.</p>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-pinjam').forEach(button => {
            button.addEventListener('click', function() {
                let form = this.closest('form');
                
                Swal.fire({
                    title: 'Konfirmasi Peminjaman',
                    text: "Yakin ingin meminjam buku ini? Pastikan dikembalikan tepat waktu ya!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb', // Blue
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Pinjam!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>