<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @php
                $totalUnpaidFine = $historyLoans->where('payment_status', 'pending')->sum('fine_amount');
            @endphp

            @if($totalUnpaidFine > 0)
            <div class="bg-red-600 text-white rounded-lg shadow-lg mb-6 p-4 flex items-center animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mr-4 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                
                <div>
                    <h3 class="font-bold text-lg uppercase tracking-wide">⚠️ Akun Dibekukan Sementara</h3>
                    <p class="mt-1">
                        Anda memiliki total tunggakan denda sebesar 
                        <span class="font-black text-2xl text-yellow-300 ml-1">Rp {{ number_format($totalUnpaidFine, 0, ',', '.') }}</span>
                    </p>
                    <p class="text-xs mt-1 text-red-100">
                        Harap segera lakukan pelunasan di meja petugas perpustakaan untuk membuka blokir peminjaman.
                    </p>
                </div>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-purple-600">🔔 Notifikasi Terbaru</h3>
                        <a href="{{ route('notifications.index') }}" class="text-sm text-purple-500 hover:text-purple-700 underline">
                            Lihat Semua Riwayat →
                        </a>
                    </div>                    
                    @forelse($notifications as $notif)
                        <div class="mb-2 p-3 bg-purple-50 rounded border-l-4 border-purple-400">
                            <p class="text-sm text-gray-800">{{ $notif->data['message'] }}</p>
                            <span class="text-xs text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 italic text-sm">Belum ada notifikasi baru.</p>
                    @endforelse
                </div>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-blue-600">📚 Sedang Dipinjam</h3>
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

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
                                        <th class="py-2 px-4 border-b text-center">Status / Denda</th>
                                        <th class="py-2 px-4 border-b text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeLoans as $loan)
                                    @php
                                        // Hitung Estimasi Denda
                                        $today = \Carbon\Carbon::now();
                                        $dueDate = \Carbon\Carbon::parse($loan->due_date);
                                        $lateDays = 0;
                                        $estimatedFine = 0;

                                        if ($today->gt($dueDate)) {
                                            $lateDays = $today->diffInDays($dueDate);
                                            $estimatedFine = $lateDays * $loan->book->fine_per_day;
                                        }
                                    @endphp

                                    <tr>
                                        <td class="py-2 px-4 border-b font-medium">{{ $loan->book->title }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $loan->loan_date }}</td>
                                        <td class="py-2 px-4 border-b text-center text-red-600 font-bold">
                                            {{ $loan->due_date }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            @if($estimatedFine > 0)
                                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">
                                                    Telat {{ $lateDays }} Hari
                                                </span>
                                                <div class="text-xs text-red-600 mt-1 font-bold">
                                                    Estimasi Denda: Rp {{ number_format($estimatedFine, 0, ',', '.') }}
                                                </div>
                                            @else
                                                <span class="bg-yellow-200 text-yellow-800 text-xs px-2 py-1 rounded-full">
                                                    Dipinjam
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <form action="{{ route('loans.renew', $loan->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white text-xs px-3 py-1 rounded shadow">
                                                    Perpanjang (+3 Hari)
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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
                                                @if($loan->payment_status == 'pending')
                                                    <div class="text-xs bg-red-100 text-red-800 px-1 rounded mt-1 inline-block">Belum Lunas</div>
                                                @else
                                                    <div class="text-xs bg-green-100 text-green-800 px-1 rounded mt-1 inline-block">Lunas</div>
                                                @endif
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-indigo-600">✨ Rekomendasi Untukmu</h3>
                    <p class="text-sm text-gray-500 mb-4">Berdasarkan buku yang terakhir kamu baca.</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @forelse($recommendations as $book)
                        <div class="border rounded-lg p-4 hover:shadow-lg transition">
                            <div class="h-32 bg-indigo-100 rounded mb-3 flex items-center justify-center">
                                <span class="text-indigo-300 font-bold text-xl">BOOK</span>
                            </div>
                            <h4 class="font-bold text-gray-800 truncate">{{ $book->title }}</h4>
                            <p class="text-xs text-gray-500 mb-2">{{ $book->author }}</p>
                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ $book->category }}</span>
                            
                            <a href="{{ route('books.show', $book->id) }}" class="block mt-3 text-center bg-indigo-500 text-white text-xs font-bold py-2 rounded hover:bg-indigo-600">
                                Lihat Detail
                            </a>
                        </div>
                        @empty
                            <p class="text-gray-500 italic">Belum ada rekomendasi saat ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>