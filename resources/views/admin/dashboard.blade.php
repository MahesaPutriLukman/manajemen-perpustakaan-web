<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Selamat Datang, Admin!</h3>
                    <p>Ini adalah halaman khusus untuk mengelola User dan Buku.</p>
                    
                    <div class="mt-4 flex gap-4">
                        <a href="{{ route('books.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow-lg transition duration-200">
                            Kelola Buku
                        </a>
                        
                        <a href="{{ route('users.index') }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded shadow-lg transition duration-200">
                            Kelola User
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>