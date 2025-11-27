<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-blue-600">📚 Sedang Dipinjam</h3>
                    
                    @if($activeLoans->isEmpty())
                        <p class="text-gray-500 italic">Kamu tidak sedang meminjam buku apapun.</p>
                        <a href="{{ route('books.index') }}" class="mt-2 inline-block text-sm text-blue-500 hover:underline">-> Cari Buku di Koleksi</a>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Judul Buku</th>
                                        <th class="py-2 px-4 border-b text-center">Tanggal Pinjam</th>
                                        <th class="py-2 px-4 border-b text-center">Wajib Kembali</th>
                                        <th class="py-2 px-4 border-b text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeLoans as $loan)
                                    <tr>
                                        <td class="py-2 px-4 border-b font-medium">{{ $loan->book->title }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $loan->loan_date }}</td>
                                        <td class="py-2 px-4 border-b text-center text-red-600 font-bold">
                                            {{ $loan->due_date }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="bg-yellow-200 text-yellow-800 text-xs px-2 py-1 rounded-full">
                                                Dipinjam
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-gray-700">📜 Riwayat Peminjaman</h3>

                    @if($historyLoans->isEmpty())
                        <p class="text-gray-500 italic">Belum ada riwayat peminjaman.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">Judul Buku</th>
                                        <th class="py-2 px-4 border-b text-center">Tanggal Pinjam</th>
                                        <th class="py-2 px-4 border-b text-center">Tanggal Kembali</th>
                                        <th class="py-2 px-4 border-b text-center">Denda</th>
                                        <th class="py-2 px-4 border-b text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyLoans as $loan)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">{{ $loan->book->title }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $loan->loan_date }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $loan->return_date }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            @if($loan->fine_amount > 0)
                                                <span class="text-red-600 font-bold">Rp {{ number_format($loan->fine_amount, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-green-600">-</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <span class="bg-green-200 text-green-800 text-xs px-2 py-1 rounded-full">
                                                Selesai
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>