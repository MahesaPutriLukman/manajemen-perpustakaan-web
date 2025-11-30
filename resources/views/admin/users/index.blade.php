<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('users.create') }}" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded mb-4 inline-block shadow">
                        + Tambah User Baru
                    </a>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">Nama</th>
                                    <th class="py-2 px-4 border-b text-left">Email</th>
                                    <th class="py-2 px-4 border-b text-center">Role</th>
                                    <th class="py-2 px-4 border-b text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b font-bold">{{ $user->name }}</td>
                                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        @if($user->role == 'admin')
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-bold">Admin</span>
                                        @elseif($user->role == 'pegawai')
                                            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-bold">Pegawai</span>
                                        @else
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-bold">Mahasiswa</span>
                                        @endif
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-2 font-bold text-sm">Edit</a>
                                        
                                        @if(auth()->id() != $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-900 font-bold text-sm btn-delete">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                let form = this.closest('form');
                Swal.fire({
                    title: 'Yakin hapus user ini?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
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