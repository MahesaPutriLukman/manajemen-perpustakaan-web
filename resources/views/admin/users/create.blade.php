<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah User Baru</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Gagal Menyimpan!</strong>
                        <ul class="mt-1 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Role (Peran)</label>
                        <select name="role" class="w-full border rounded px-3 py-2 bg-white">
                            <option value="mahasiswa">Mahasiswa</option>
                            <option value="pegawai">Pegawai</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Password</label>
                        <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold w-full hover:bg-blue-800">
                        Simpan User
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>