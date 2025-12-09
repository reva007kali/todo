<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskReminder;
use Carbon\Carbon;

class SendTaskReminders extends Command
{
    protected $signature = 'tasks:remind';
    protected $description = 'Cek deadline tugas dan kirim notifikasi PWA';

    public function handle()
    {
        $this->info('Memulai pengecekan tugas...');

        // --- 1. FITUR DEADLINE REMINDER (H-1 Jam) ---
        // Cari tugas pending yang due_date-nya antara SEKARANG dan 1 JAM KE DEPAN
        // Dan pastikan user punya subscription push notification
        $tasks = Task::where('status', 'pending')
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [now(), now()->addHour()])
            ->whereHas('user.pushSubscriptions') // Hanya user yang sudah subscribe
            ->with('user')
            ->get();

        foreach ($tasks as $task) {
            // Cek logik sederhana agar tidak spam notif berkali-kali (opsional: tambah kolom 'notified_at' di DB)
            // Di sini kita kirim saja sebagai contoh

            try {
                $task->user->notify(new TaskReminder(
                    "⏳ Segera Jatuh Tempo!",
                    "Tugas '{$task->title}' harus selesai jam " . $task->due_date->format('H:i'),
                    url('/tasks') // Arahkan ke halaman list
                ));
                $this->info("Notif dikirim ke: {$task->user->name} untuk tugas: {$task->title}");
            } catch (\Exception $e) {
                $this->error("Gagal kirim ke {$task->user->name}: " . $e->getMessage());
            }
        }

        // --- 2. FITUR DAILY BRIEFING (Hanya jam 07:00 pagi) ---
        // Kita cek jam sekarang, jika jam 7 pagi, jalankan blast
        if (now()->format('H:i') == '07:00') {
            $users = User::whereHas('pushSubscriptions')->get();

            foreach ($users as $user) {
                $pendingCount = $user->tasks()->where('status', 'pending')->count();

                if ($pendingCount > 0) {
                    $user->notify(new TaskReminder(
                        "🌞 Semangat Pagi, {$user->name}!",
                        "Kamu memiliki $pendingCount tugas yang menunggu penyelesaian hari ini.",
                        url('/dashboard')
                    ));
                }
            }
            $this->info('Daily briefing terkirim.');
        }
    }
}