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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-bold text-blue-700">📚 Daftar Buku yang Sedang Dipinjam</h3>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('pegawai.notifications') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded shadow-lg text-sm flex items-center transition duration-200 transform hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                Lihat Log
                            </a>

                            @if($loans->isEmpty())
                                <button type="button" disabled class="bg-purple-300 text-white font-bold py-2 px-4 rounded shadow-none text-sm flex items-center cursor-not-allowed opacity-60" title="Tidak ada peminjaman aktif saat ini">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Kirim Pengingat Jatuh Tempo
                                </button>
                            @else
                                <a href="{{ route('pegawai.trigger.reminders') }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded shadow-lg text-sm flex items-center transition duration-200 transform hover:scale-105">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    Kirim Pengingat Jatuh Tempo
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Peminjam</th>
                                    <th class="py-2 px-4 border-b text-left">Buku</th>
                                    <th class="py-2 px-4 border-b text-center">Tgl Pinjam</th>
                                    <th class="py-2 px-4 border-b text-center">Jatuh Tempo</th>
                                    <th class="py-2 px-4 border-b text-center">Status Denda (Estimasi)</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loans as $loan)
                                @php
                                    // LOGIKA HITUNG DENDA SEMENTARA
                                    $today = \Carbon\Carbon::now()->startOfDay();
                                    $dueDate = \Carbon\Carbon::parse($loan->due_date)->startOfDay();
                                    
                                    $lateDays = 0;
                                    $estimatedFine = 0;

                                    if ($today->gt($dueDate)) {
                                        // Paksa positif dengan abs()
                                        $lateDays = abs($today->diffInDays($dueDate));
                                        $estimatedFine = $lateDays * $loan->book->fine_per_day;
                                    }
                                @endphp

                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $loan->user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $loan->book->title }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $loan->loan_date->format('d-m-Y') }}</td>
                                    
                                    <td class="py-2 px-4 border-b text-center font-bold {{ $estimatedFine > 0 ? 'text-red-600' : 'text-gray-800' }}">
                                        {{ $loan->due_date->format('d-m-Y') }}
                                    </td>

                                    <td class="py-2 px-4 border-b text-center">
                                        @if($estimatedFine > 0)
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-bold">
                                                Telat {{ $lateDays }} Hari
                                            </span>
                                            <div class="text-xs text-red-600 mt-1 font-bold">
                                                Rp {{ number_format($estimatedFine, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-bold">
                                                Aman
                                            </span>
                                        @endif
                                    </td>

                                    <td class="py-2 px-4 border-b text-center">
                                        <form action="{{ route('loans.return', $loan->id) }}" method="POST" class="confirm-return-form">
                                            @csrf
                                            <button type="button" class="bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-1 px-3 rounded text-sm shadow btn-confirm-return" data-denda="{{ $estimatedFine }}">
                                                Konfirmasi Kembali
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">
                                        Tidak ada buku yang sedang dipinjam saat ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4 text-red-600">💸 Daftar Denda Belum Lunas</h3>
                    
                    @php
                        // Ambil data denda pending
                        $unpaidFines = App\Models\Loan::with('user')->where('payment_status', 'pending')->get();
                    @endphp

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-red-200">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Peminjam</th>
                                    <th class="py-2 px-4 border-b text-left">Total Denda</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($unpaidFines as $fine)
                                <tr>
                                    <td class="py-2 px-4 border-b font-medium">{{ $fine->user->name }}</td>
                                    <td class="py-2 px-4 border-b text-red-600 font-bold">
                                        Rp {{ number_format($fine->fine_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <form action="{{ route('loans.pay', $fine->id) }}" method="POST" class="confirm-pay-form">
                                            @csrf
                                            <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm shadow btn-confirm-pay">
                                                Tandai Lunas ✅
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-500">
                                        Tidak ada tunggakan denda saat ini. Aman!
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // 1. Konfirmasi Pengembalian Buku
        document.querySelectorAll('.btn-confirm-return').forEach(button => {
            button.addEventListener('click', function() {
                let form = this.closest('form');
                let denda = this.getAttribute('data-denda');
                // Format angka ke Rupiah
                let formattedDenda = new Intl.NumberFormat('id-ID').format(denda);

                let pesanDenda = denda > 0 
                    ? "Mahasiswa ini kena denda Rp " + formattedDenda + ". Pastikan sudah diinfokan!" 
                    : "Tidak ada denda. Aman.";

                Swal.fire({
                    title: 'Terima Buku Kembali?',
                    text: pesanDenda,
                    icon: denda > 0 ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4338ca', // Indigo
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Terima Buku',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        // 2. Konfirmasi Pelunasan Denda
        document.querySelectorAll('.btn-confirm-pay').forEach(button => {
            button.addEventListener('click', function() {
                let form = this.closest('form');
                
                Swal.fire({
                    title: 'Konfirmasi Pelunasan?',
                    text: "Pastikan uang denda sudah diterima.",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a', // Green
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Lunas!',
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