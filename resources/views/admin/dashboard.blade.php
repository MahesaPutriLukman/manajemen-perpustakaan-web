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
                        <button class="bg-blue-500 text-white px-4 py-2 rounded">Kelola Buku</button>
                        <button class="bg-green-500 text-white px-4 py-2 rounded">Kelola User</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>