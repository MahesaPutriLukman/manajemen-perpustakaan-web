<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong class="font-bold">Gagal Update!</strong>
                            <ul class="mt-1 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('books.update', $book->id) }}" method="POST">
                        @csrf
                        @method('PUT') <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Judul Buku</label>
                            <input type="text" name="title" value="{{ old('title', $book->title) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Penulis</label>
                                <input type="text" name="author" value="{{ old('author', $book->author) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Penerbit</label>
                                <input type="text" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Terbit</label>
                                <input type="number" name="publication_year" value="{{ old('publication_year', $book->publication_year) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="category" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white">
                                    @foreach(['Fiksi', 'Sains', 'Teknologi', 'Sejarah', 'Lainnya'] as $cat)
                                        <option value="{{ $cat }}" {{ (old('category', $book->category) == $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Stok</label>
                                <input type="number" name="stock" value="{{ old('stock', $book->stock) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Max Pinjam (Hari)</label>
                                <input type="number" name="max_loan_days" value="{{ old('max_loan_days', $book->max_loan_days) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Denda / Hari (Rp)</label>
                                <input type="number" name="fine_per_day" value="{{ old('fine_per_day', $book->fine_per_day) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('books.index') }}" class="text-gray-500 hover:text-gray-700">Batal</a>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Buku
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>