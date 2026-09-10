<?php

namespace App\Http\Controllers;

use App\Models\BellLog;
use App\Models\Setting;
use App\Models\Sound;
use App\Services\ScheduleResolverService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(ScheduleResolverService $resolver)
    {
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($timezone);
        $resolution = $resolver->getActiveSchedulesForToday($now);

        $currentTimeStr = $now->format('H:i');

        // Tentukan jadwal berikutnya (next bell)
        $nextBell = null;
        foreach ($resolution['schedules'] as &$item) {
            $item['is_passed'] = strcmp($item['time'], $currentTimeStr) < 0;
            $item['is_current'] = ($item['time'] === $currentTimeStr);

            if (!$nextBell && strcmp($item['time'], $currentTimeStr) >= 0) {
                $nextBell = $item;
            }
        }

        $allSounds = Sound::orderBy('language')->orderBy('name')->get();
        $isMuted = (bool) Setting::get('mute_all', false);
        $masterVolume = (float) Setting::get('master_volume', 1.0);
        $schoolName = Setting::get('school_name', 'Sistem Bel Sekolah Otomatis');

        return view('pages.dashboard', [
            'todayDate' => $resolution['formatted_date'],
            'statusType' => $resolution['status_type'],
            'statusMessage' => $resolution['status_message'],
            'schedules' => $resolution['schedules'],
            'nextBell' => $nextBell,
            'allSounds' => $allSounds,
            'isMuted' => $isMuted,
            'masterVolume' => $masterVolume,
            'schoolName' => $schoolName,
        ]);
    }

    /**
     * API Status Realtime untuk Polling & Sinkronisasi Audio Player
     */
    public function apiStatus(ScheduleResolverService $resolver): JsonResponse
    {
        $timezone = config('app.timezone', 'Asia/Jakarta');
        $now = Carbon::now($timezone);
        $resolution = $resolver->getActiveSchedulesForToday($now);
        $currentTimeStr = $now->format('H:i');

        $nextBell = null;
        foreach ($resolution['schedules'] as &$item) {
            $item['is_passed'] = strcmp($item['time'], $currentTimeStr) < 0;
            if (!$nextBell && strcmp($item['time'], $currentTimeStr) >= 0) {
                $nextBell = $item;
            }
        }

        // Cek apakah ada pemicu bel baru di cache
        $latestTrigger = Cache::get('latest_bell_trigger');

        return response()->json([
            'server_time' => $now->format('H:i:s'),
            'server_date' => $now->format('Y-m-d'),
            'date_formatted' => $now->locale('id')->isoFormat('dddd, D MMMM Y'),
            'status_type' => $resolution['status_type'],
            'status_message' => $resolution['status_message'],
            'next_bell' => $nextBell,
            'schedules' => $resolution['schedules'],
            'is_muted' => (bool) Setting::get('mute_all', false),
            'master_volume' => (float) Setting::get('master_volume', 1.0),
            'latest_trigger' => $latestTrigger,
        ]);
    }

    /**
     * Pemicu Bel Manual (Darurat atau Uji Coba)
     */
    public function triggerManual(Request $request): JsonResponse
    {
        $request->validate([
            'sound_id' => 'required|exists:sounds,id',
            'label' => 'nullable|string|max:100',
        ]);

        $sound = Sound::findOrFail($request->sound_id);
        $label = $request->label ?: 'Bel Manual / Pengumuman';
        $now = Carbon::now();

        $log = BellLog::create([
            'triggered_at' => $now,
            'source_type' => 'manual',
            'label' => $label,
            'sound_name' => $sound->name,
            'language' => $sound->language,
            'status' => 'success',
            'notes' => 'Dibunyikan secara manual oleh operator',
        ]);

        $triggerPayload = [
            'id' => $log->id,
            'time' => $now->format('H:i'),
            'label' => $label,
            'sound_url' => asset($sound->file_path),
            'sound_name' => $sound->name,
            'language' => $sound->language,
            'timestamp' => microtime(true),
        ];

        Cache::put('latest_bell_trigger', $triggerPayload, now()->addMinutes(2));

        return response()->json([
            'success' => true,
            'message' => "Bel '{$sound->name}' berhasil dibunyikan!",
            'payload' => $triggerPayload,
        ]);
    }

    /**
     * Toggle Mode Hening (Mute All)
     */
    public function toggleMute(): JsonResponse
    {
        $current = (bool) Setting::get('mute_all', false);
        $newVal = !$current;
        Setting::set('mute_all', $newVal ? '1' : '0');

        return response()->json([
            'success' => true,
            'is_muted' => $newVal,
            'message' => $newVal ? 'Mode hening diaktifkan (semua bel dinonaktifkan sementara).' : 'Mode hening dinonaktifkan (bel normal kembali).',
        ]);
    }
}
