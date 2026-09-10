<?php

namespace App\Services;

use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\SpecialSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ScheduleResolverService
{
    /**
     * Cek apakah tanggal tertentu adalah hari libur (baik tanggal spesifik maupun libur mingguan)
     */
    public function getHoliday(Carbon $date): ?Holiday
    {
        $dateStr = $date->toDateString(); // Y-m-d
        $dayOfWeek = $date->format('D'); // Mon, Tue, Wed, Thu, Fri, Sat, Sun

        // 1. Cek tanggal spesifik
        $specific = Holiday::whereDate('date', $dateStr)->first();
        if ($specific) {
            return $specific;
        }

        // 2. Cek libur mingguan (contoh: Minggu / Sabtu)
        $recurring = Holiday::where('is_recurring_weekly', true)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        return $recurring;
    }

    /**
     * Cek apakah ada jadwal khusus pada tanggal tertentu
     */
    public function getSpecialSchedules(Carbon $date): Collection
    {
        return SpecialSchedule::with('sound')
            ->whereDate('date', $date->toDateString())
            ->orderBy('time')
            ->get();
    }

    /**
     * Resolusi jadwal yang aktif hari ini berdasarkan prioritas
     */
    public function getActiveSchedulesForToday(Carbon $date): array
    {
        $holiday = $this->getHoliday($date);
        $specialSchedules = $this->getSpecialSchedules($date);
        $dayOfWeek = $date->format('D');

        $activeItems = [];
        $statusMessage = 'Normal';
        $statusType = 'normal'; // 'normal', 'holiday', 'special'

        // Skenario 1: Ada Jadwal Khusus
        if ($specialSchedules->isNotEmpty()) {
            $overridesRegular = $specialSchedules->contains('overrides_regular', true);
            $statusType = 'special';
            $statusMessage = 'Jadwal Khusus Aktif (' . $specialSchedules->first()->label . ')';

            foreach ($specialSchedules as $item) {
                $activeItems[] = [
                    'id' => 'special_' . $item->id,
                    'time' => $item->time,
                    'label' => $item->label,
                    'sound_id' => $item->sound_id,
                    'sound_name' => $item->sound?->name ?? 'Default Chime',
                    'sound_url' => $item->sound?->file_path ? asset($item->sound->file_path) : asset('audio/id/chime.wav'),
                    'language' => $item->language,
                    'source_type' => 'special',
                    'is_active' => true,
                ];
            }

            // Jika tidak override reguler dan bukan hari libur, gabungkan reguler
            if (!$overridesRegular && !$holiday) {
                $regular = $this->getRegularSchedulesForDay($dayOfWeek);
                foreach ($regular as $item) {
                    $activeItems[] = $item;
                }
            }
        }
        // Skenario 2: Hari Libur (tanpa jadwal khusus)
        elseif ($holiday) {
            $statusType = 'holiday';
            $statusMessage = 'Hari Libur: ' . $holiday->description;
            // Jadwal kosong karena hari libur
        }
        // Skenario 3: Hari Normal
        else {
            $activeItems = $this->getRegularSchedulesForDay($dayOfWeek);
            $statusType = 'normal';
            $statusMessage = 'Jadwal Reguler Aktif (' . $date->locale('id')->isoFormat('dddd') . ')';
        }

        // Urutkan berdasarkan jam
        usort($activeItems, function ($a, $b) {
            return strcmp($a['time'], $b['time']);
        });

        return [
            'date' => $date->toDateString(),
            'formatted_date' => $date->locale('id')->isoFormat('dddd, D MMMM Y'),
            'status_type' => $statusType,
            'status_message' => $statusMessage,
            'holiday' => $holiday,
            'schedules' => $activeItems,
        ];
    }

    /**
     * Ambil jadwal reguler untuk nama hari tertentu
     */
    protected function getRegularSchedulesForDay(string $dayOfWeek): array
    {
        $schedules = Schedule::with('sound')
            ->where('is_active', true)
            ->orderBy('time')
            ->get();

        $items = [];
        foreach ($schedules as $item) {
            $days = $item->days_of_week ?? [];
            if (in_array($dayOfWeek, $days)) {
                $items[] = [
                    'id' => 'regular_' . $item->id,
                    'time' => $item->time,
                    'label' => $item->label,
                    'sound_id' => $item->sound_id,
                    'sound_name' => $item->sound?->name ?? 'Default Chime',
                    'sound_url' => $item->sound?->file_path ? asset($item->sound->file_path) : asset('audio/id/chime.wav'),
                    'language' => $item->language,
                    'source_type' => 'regular',
                    'is_active' => $item->is_active,
                ];
            }
        }

        return $items;
    }
}
