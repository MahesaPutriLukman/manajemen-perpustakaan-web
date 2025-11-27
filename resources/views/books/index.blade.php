<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Buku Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(Auth::user()->role !== 'mahasiswa')
                        <div class="mb-6">
                            <a href="{{ route('books.create') }}" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded shadow-lg">
                                + Tambah Buku Baru
                            </a>
                        </div>
                    @endif

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
                                    <td class="py-2 px-4 border-b font-medium">{{ $book->title }}</td>
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
                                        Belum ada buku yang tersedia. <br>
                                        <span class="text-sm">Silakan klik tombol tambah di atas.</span>
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