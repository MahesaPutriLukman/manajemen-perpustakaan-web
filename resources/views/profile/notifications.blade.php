<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Notifikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-6 text-indigo-600">📬 Kotak Masuk Notifikasi</h3>

                    @if($notifications->isEmpty())
                        <p class="text-center text-gray-500 py-10">Belum ada riwayat notifikasi.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-4 border-b text-left text-sm font-bold text-gray-600">Pesan</th>
                                        <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-600">Waktu</th>
                                        <th class="py-3 px-4 border-b text-center text-sm font-bold text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($notifications as $notif)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="py-4 px-4 border-b text-gray-800">
                                            {{ $notif->data['message'] }}
                                        </td>
                                        <td class="py-4 px-4 border-b text-center text-sm text-gray-500">
                                            {{ $notif->created_at->format('d M Y, H:i') }} <br>
                                            <span class="text-xs">({{ $notif->created_at->diffForHumans() }})</span>
                                        </td>
                                        <td class="py-4 px-4 border-b text-center">
                                            @if($notif->read_at)
                                                <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">Sudah Dibaca</span>
                                            @else
                                                <span class="bg-green-100 text-green-700 text-xs px-2 py-1 rounded-full font-bold">Baru</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>