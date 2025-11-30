<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Loan;
use App\Notifications\GeneralNotification;
use Carbon\Carbon;

class SendLoanReminders extends Command
{
    /**
     * Nama perintah (Signature) 
     */
    protected $signature = 'pustaka:send-reminders';

    /**
     * Keterangan perintah.
     */
    protected $description = 'Mengirim notifikasi pengingat H-1 dan H-0 jatuh tempo ke mahasiswa.';

    /**
     * Eksekusi perintah.
     */
    public function handle()
    {
        $this->info('Memulai proses pengecekan jatuh tempo...');

        // 1. Tentukan Tanggal PENTING 
        $today = Carbon::now()->startOfDay();      // Hari Ini
        $tomorrow = Carbon::now()->addDay()->startOfDay(); // Besok

        // 2. Cari Peminjaman yang jatuh tempo hari ini (H-0)
        $dueToday = Loan::with('user', 'book')
                        ->where('status', 'borrowed')
                        ->whereDate('due_date', $today)
                        ->get();

        foreach ($dueToday as $loan) {
            $message = "⚠️ PENGINGAT TERAKHIR: Buku '" . $loan->book->title . "' harus dikembalikan HARI INI sebelum perpustakaan tutup.";
            // Kirim Notifikasi
            $loan->user->notify(new GeneralNotification($message));
        }

        // 3. Cari Peminjaman yang jatuh tempo besok (H-1)
        $dueTomorrow = Loan::with('user', 'book')
                           ->where('status', 'borrowed')
                           ->whereDate('due_date', $tomorrow)
                           ->get();

        foreach ($dueTomorrow as $loan) {
            $message = "📅 PENGINGAT: Buku '" . $loan->book->title . "' jatuh tempo BESOK. Jangan lupa dikembalikan ya!";
            // Kirim Notifikasi
            $loan->user->notify(new GeneralNotification($message));
        }

        $this->info('Selesai! Semua pengingat telah dikirim.');
    }
}