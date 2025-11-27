<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pegawai - Transaksi Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('warning'))
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4">
                    {{ session('warning') }}
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Daftar Buku yang Sedang Dipinjam</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Peminjam</th>
                                    <th class="py-2 px-4 border-b text-left">Buku</th>
                                    <th class="py-2 px-4 border-b text-center">Tgl Pinjam</th>
                                    <th class="py-2 px-4 border-b text-center">Jatuh Tempo</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $loan->user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $loan->book->title }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $loan->loan_date }}</td>
                                    <td class="py-2 px-4 border-b text-center text-red-600 font-bold">
                                        {{ $loan->due_date }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <form action="{{ route('loans.return', $loan->id) }}" method="POST" onsubmit="return confirm('Apakah buku ini sudah dikembalikan fisik?');">
                                            @csrf
                                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-1 px-3 rounded text-sm">
                                                Konfirmasi Kembali
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        Tidak ada buku yang sedang dipinjam saat ini.
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