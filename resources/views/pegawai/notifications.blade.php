<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Log Notifikasi Sistem') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-700">📋 Riwayat Pesan Keluar</h3>
                        <a href="{{ route('pegawai.dashboard') }}" class="text-sm text-blue-600 hover:underline">
                            &larr; Kembali ke Dashboard
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-600">Waktu Kirim</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-600">Penerima (User ID)</th>
                                    <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-600">Isi Pesan</th>
                                    <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-600">Status Baca</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    @php
                                        // Decode data JSON biar bisa dibaca
                                        $data = json_decode($log->data);
                                        $user = \App\Models\User::find($log->notifiable_id);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 border-b text-sm text-gray-500">
                                            {{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i:s') }}
                                        </td>
                                        <td class="py-3 px-4 border-b font-bold text-gray-700">
                                            {{ $user ? $user->name : 'User #'.$log->notifiable_id }}
                                        </td>
                                        <td class="py-3 px-4 border-b text-sm text-gray-600">
                                            {{ $data->message ?? 'Pesan sistem' }}
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            @if($log->read_at)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Dibaca</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full">Belum</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-500">Belum ada log notifikasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $logs->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>