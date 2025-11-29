<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard & Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-8 flex gap-4">
                <a href="{{ route('books.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center transition transform hover:scale-105">
                    📚 Kelola Buku
                </a>
                <a href="{{ route('users.index') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg flex items-center transition transform hover:scale-105">
                    👥 Kelola User
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm font-bold uppercase">Total Judul Buku</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalBooks }}</div>
                    <div class="text-xs text-gray-400">Total Stok: {{ $totalStock }} pcs</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm font-bold uppercase">Sedang Dipinjam</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $activeLoans }}</div>
                    <div class="text-xs text-gray-400">{{ $returnedLoans }} Transaksi Selesai</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm font-bold uppercase">Denda Terkumpul</div>
                    <div class="text-3xl font-bold text-green-600">Rp {{ number_format($totalFinesPaid / 1000, 0) }}k</div>
                    <div class="text-xs text-gray-400">Total: Rp {{ number_format($totalFinesPaid) }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm font-bold uppercase">Denda Tertunggak</div>
                    <div class="text-3xl font-bold text-red-600">Rp {{ number_format($totalFinesPending / 1000, 0) }}k</div>
                    <div class="text-xs text-gray-400">Harus Ditagih</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="md:col-span-2 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📊 Statistik Peminjaman per Kategori</h3>
                    <canvas id="loanChart" height="150"></canvas>
                </div>

                <div class="md:col-span-1 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">🏆 5 Buku Terpopuler</h3>
                    <ul>
                        @forelse($popularBooks as $index => $book)
                            <li class="flex justify-between items-center py-3 border-b last:border-0">
                                <div>
                                    <span class="font-bold text-gray-700">#{{ $index + 1 }}</span>
                                    <span class="ml-2 text-gray-600 text-sm">{{ Str::limit($book->title, 20) }}</span>
                                </div>
                                <span class="bg-indigo-100 text-indigo-800 text-xs font-bold px-2 py-1 rounded-full">
                                    {{ $book->loans_count }}x Pinjam
                                </span>
                            </li>
                        @empty
                            <p class="text-gray-500 text-sm italic">Belum ada data peminjaman.</p>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('loanChart').getContext('2d');
        const loanChart = new Chart(ctx, {
            type: 'bar', // Bisa ganti 'pie' atau 'line'
            data: {
                labels: {!! json_encode($chartData->keys()) !!}, // Kategori Buku
                datasets: [{
                    label: 'Jumlah Peminjaman',
                    data: {!! json_encode($chartData->values()) !!}, // Jumlah Data
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</x-app-layout>