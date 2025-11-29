<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Buku Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-6">
                        @if(session('success'))
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">Berhasil!</strong>
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative animate-pulse">
                                <strong class="font-bold">⛔ AKSES DITOLAK!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        
                        <div class="w-full md:w-auto">
                            @if(Auth::user()->role !== 'mahasiswa')
                                <a href="{{ route('books.create') }}" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-lg block text-center md:inline-block">
                                    + Tambah Buku
                                </a>
                            @endif
                        </div>

                        <form method="GET" action="{{ route('books.index') }}" class="w-full md:w-auto flex flex-col md:flex-row gap-2">
                            
                            <select name="category" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>

                            <select name="sort" class="border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Paling Baru Diinput</option>
                                <option value="year_desc" {{ request('sort') == 'year_desc' ? 'selected' : '' }}>Tahun Terbit (Baru-Lama)</option>
                                <option value="year_asc" {{ request('sort') == 'year_asc' ? 'selected' : '' }}>Tahun Terbit (Lama-Baru)</option>
                                <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>Judul (A-Z)</option>
                                <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok Terbanyak</option>
                            </select>

                            <div class="flex">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul/penulis..." class="border-gray-300 rounded-l-md text-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-48">
                                <button type="submit" class="bg-gray-800 text-white px-4 rounded-r-md hover:bg-gray-700 text-sm">Cari</button>
                            </div>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-600">Judul</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-600">Penulis</th>
                                    <th class="py-3 px-4 border-b text-left font-bold text-gray-600">Kategori</th>
                                    <th class="py-3 px-4 border-b text-center font-bold text-gray-600">Stok</th>
                                    <th class="py-3 px-4 border-b text-center font-bold text-gray-600">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($books as $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">
                                        <a href="{{ route('books.show', $book->id) }}" class="font-bold text-blue-600 hover:underline">
                                            {{ $book->title }}
                                        </a>
                                    </td>                                   
                                    <td class="py-2 px-4 border-b">{{ $book->author }}</td>
                                    <td class="py-2 px-4 border-b">
                                        <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs">
                                            {{ $book->category }}
                                        </span>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        {{ $book->stock }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if(Auth::user()->role !== 'mahasiswa')
                                            <a href="{{ route('books.edit', $book->id) }}" class="text-blue-600 hover:text-blue-900 text-sm mr-2">Edit</a>
                                        @else
                                            @if($book->stock > 0)
                                                <form action="{{ route('loans.store') }}" method="POST" class="inline">
                                                    @csrf
                                                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white text-xs font-bold py-1 px-3 rounded" onclick="return confirm('Yakin ingin meminjam buku ini?')">
                                                        Pinjam
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs italic">Stok Habis</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        Data tidak ditemukan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>