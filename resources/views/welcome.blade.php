<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Digital</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased bg-gray-50 text-gray-800">

    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex-shrink-0 flex items-center">
                    <h1 class="text-2xl font-bold text-blue-600">📚 PerpusDigital</h1>
                </div>

                <div class="flex items-center space-x-4">
                    @if (Route::has('login'))
                        @auth
                            @php
                                $role = Auth::user()->role;
                                $dashboardRoute = $role == 'admin' ? 'admin.dashboard' : ($role == 'pegawai' ? 'pegawai.dashboard' : 'dashboard');
                            @endphp
                            <a href="{{ route($dashboardRoute) }}" class="text-gray-700 hover:text-blue-600 font-semibold">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-semibold">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-semibold text-sm">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="bg-blue-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Temukan Jendela Duniamu</h1>
            <p class="text-blue-100 text-lg mb-8">Jelajahi koleksi buku terbaru dan terlengkap di universitas kami.</p>
            
            <form action="/" method="GET" class="max-w-2xl mx-auto flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul buku, penulis, atau kategori..." 
                       class="w-full px-5 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 shadow-lg text-gray-800">
                <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 font-bold px-6 py-3 rounded-lg shadow-lg">Cari</button>
            </form>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">📖 Koleksi Buku Terbaru</h2>
            @if(request('search'))
                <a href="/" class="text-blue-600 hover:underline text-sm">Reset Pencarian</a>
            @endif
        </div>

        @if($books->isEmpty())
            <div class="text-center py-12 bg-white rounded-lg shadow">
                <p class="text-gray-500 text-lg">Maaf, buku yang kamu cari tidak ditemukan.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($books as $book)
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                    <div class="h-40 bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center">
                        <span class="text-white font-bold text-3xl opacity-30">BOOK</span>
                    </div>
                    
                    <div class="p-5">
                        <div class="text-xs font-bold text-blue-500 mb-2 uppercase tracking-wide">{{ $book->category }}</div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 truncate">{{ $book->title }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ $book->author }}</p>
                        
                        <div class="flex justify-between items-center border-t pt-4">
                            <div class="text-sm text-gray-500">
                                Stok: <span class="font-bold {{ $book->stock > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $book->stock }}</span>
                            </div>
                            
                            @auth
                                @if(Auth::user()->role == 'mahasiswa')
                                    <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Lihat & Pinjam →</a>
                                @else
                                    <span class="text-gray-400 text-sm">Admin View</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">Login untuk Pinjam</a>
                            @endauth
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    <footer class="bg-white border-t mt-12 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center text-gray-500 text-sm">
            &copy; 2025 Perpustakaan Universitas. Tugas Final Pemrograman Web.
        </div>
    </footer>

</body>
</html>