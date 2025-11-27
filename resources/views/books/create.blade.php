<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Buku Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <form action="{{ route('books.store') }}" method="POST">
                        @csrf <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Judul Buku</label>
                            <input type="text" name="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Penulis</label>
                                <input type="text" name="author" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Penerbit</label>
                                <input type="text" name="publisher" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Tahun Terbit</label>
                                <input type="number" name="publication_year" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="category" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-white">
                                    <option value="Fiksi">Fiksi</option>
                                    <option value="Sains">Sains</option>
                                    <option value="Teknologi">Teknologi</option>
                                    <option value="Sejarah">Sejarah</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Stok Awal</label>
                                <input type="number" name="stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Max Pinjam (Hari)</label>
                                <input type="number" name="max_loan_days" value="7" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Denda / Hari (Rp)</label>
                                <input type="number" name="fine_per_day" value="1000" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('books.index') }}" class="text-gray-500 hover:text-gray-700">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Simpan Buku
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>