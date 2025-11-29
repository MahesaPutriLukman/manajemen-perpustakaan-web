<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit User</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ $user->name }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full border rounded px-3 py-2" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Role (Peran)</label>
                        <select name="role" class="w-full border rounded px-3 py-2 bg-white">
                            <option value="mahasiswa" {{ $user->role == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="pegawai" {{ $user->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <hr class="my-4">
                    <p class="text-sm text-gray-500 mb-2">Kosongkan password jika tidak ingin mengganti.</p>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Password Baru (Opsional)</label>
                        <input type="password" name="password" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2">
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('users.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded font-bold w-1/2 text-center hover:bg-gray-600">Batal</a>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded font-bold w-1/2 hover:bg-yellow-600">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>