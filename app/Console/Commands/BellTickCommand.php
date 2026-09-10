<?php

namespace App\Console\Commands;

use App\Models\BellLog;
use App\Models\Setting;
use App\Services\ScheduleResolverService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class BellTickCommand extends Command
{
    protected $signature = 'bell:tick {--force-time= : Format HH:mm untuk testing simulasi}';
    protected $description = 'Mengecek jadwal bel sekolah pada menit saat ini dan memicu alarm jika cocok';

    public function handle(ScheduleResolverService $resolver): int
    {
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($timezone);

        $currentTime = $this->option('force-time') ?: $now->format('H:i');
        $todayDateStr = $now->toDateString();

        $this->info("Pemeriksaan Bel: Tanggal {$todayDateStr}, Pukul {$currentTime}");

        // Cek apakah sistem sedang dimatikan suaranya (Mute All)
        $isMuted = (bool) Setting::get('mute_all', false);
        if ($isMuted) {
            $this->warn("Mode Hening (Mute) sedang aktif. Bel tidak akan berbunyi.");
            return Command::SUCCESS;
        }

        $resolution = $resolver->getActiveSchedulesForToday($now);
        $schedules = $resolution['schedules'];

        if (empty($schedules)) {
            $this->line("Tidak ada jadwal aktif untuk hari ini ({$resolution['status_message']}).");
            return Command::SUCCESS;
        }

        $triggeredCount = 0;

        foreach ($schedules as $schedule) {
            if ($schedule['time'] === $currentTime) {
                // Pencegahan double ringing dalam 1 menit yang sama
                $dedupKey = "bell_triggered_{$todayDateStr}_{$currentTime}_{$schedule['id']}";
                if (Cache::has($dedupKey)) {
                    $this->line("Jadwal '{$schedule['label']}' sudah berbunyi pada menit ini. Dilewati.");
                    continue;
                }

                Cache::put($dedupKey, true, now()->addMinutes(2));

                // Simpan riwayat ke database
                $log = BellLog::create([
                    'triggered_at' => $now,
                    'source_type' => $schedule['source_type'],
                    'label' => $schedule['label'],
                    'sound_name' => $schedule['sound_name'],
                    'language' => $schedule['language'],
                    'status' => 'success',
                    'notes' => 'Berbunyi otomatis sesuai jadwal',
                ]);

                // Simpan payload trigger bel terakhir ke cache agar UI desktop / audio player langsung memutar
                Cache::put('latest_bell_trigger', [
                    'id' => $log->id,
                    'time' => $currentTime,
                    'label' => $schedule['label'],
                    'sound_url' => $schedule['sound_url'],
                    'sound_name' => $schedule['sound_name'],
                    'language' => $schedule['language'],
                    'timestamp' => microtime(true),
                ], now()->addMinutes(5));

                $this->info(">>> [BUNYI BEL] {$schedule['label']} ({$schedule['time']}) - Suara: {$schedule['sound_name']} <<<");
                $triggeredCount++;
            }
        }

        if ($triggeredCount === 0) {
            $this->line("Tidak ada bel yang terjadwal persis pukul {$currentTime}.");
        }

        return Command::SUCCESS;
    }
}
