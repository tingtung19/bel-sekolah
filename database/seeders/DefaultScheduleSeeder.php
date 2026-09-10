<?php

namespace Database\Seeders;

use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Sound;
use Illuminate\Database\Seeder;

class DefaultScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $soundMasukId = Sound::where('file_path', 'like', '%id/masuk%')->first()?->id;
        $soundIstirahatId = Sound::where('file_path', 'like', '%id/istirahat%')->first()?->id;
        $soundPulangId = Sound::where('file_path', 'like', '%id/pulang%')->first()?->id;

        $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'];

        $defaultSchedules = [
            [
                'time' => '07:00',
                'label' => 'Masuk Kelas / Bel Pagi',
                'days_of_week' => $weekdays,
                'sound_id' => $soundMasukId,
                'language' => 'id',
                'is_active' => true,
            ],
            [
                'time' => '09:30',
                'label' => 'Istirahat Pertama',
                'days_of_week' => $weekdays,
                'sound_id' => $soundIstirahatId,
                'language' => 'id',
                'is_active' => true,
            ],
            [
                'time' => '10:00',
                'label' => 'Masuk Kembali (Selesai Istirahat 1)',
                'days_of_week' => $weekdays,
                'sound_id' => $soundMasukId,
                'language' => 'id',
                'is_active' => true,
            ],
            [
                'time' => '12:00',
                'label' => 'Istirahat Kedua / Sholat Dhuhur',
                'days_of_week' => $weekdays,
                'sound_id' => $soundIstirahatId,
                'language' => 'id',
                'is_active' => true,
            ],
            [
                'time' => '12:45',
                'label' => 'Masuk Kembali (Selesai Istirahat 2)',
                'days_of_week' => $weekdays,
                'sound_id' => $soundMasukId,
                'language' => 'id',
                'is_active' => true,
            ],
            [
                'time' => '15:00',
                'label' => 'Bel Pulang Sekolah',
                'days_of_week' => $weekdays,
                'sound_id' => $soundPulangId,
                'language' => 'id',
                'is_active' => true,
            ],
        ];

        foreach ($defaultSchedules as $schedule) {
            Schedule::updateOrCreate(
                ['time' => $schedule['time'], 'label' => $schedule['label']],
                $schedule
            );
        }

        // Hari Libur Mingguan (Minggu)
        Holiday::updateOrCreate(
            ['is_recurring_weekly' => true, 'day_of_week' => 'Sun'],
            [
                'date' => null,
                'description' => 'Libur Mingguan (Hari Minggu)',
            ]
        );

        // Setting default
        Setting::set('school_name', 'SMP / SMA Negeri Digital Indonesia');
        Setting::set('pin_code', '1234');
        Setting::set('master_volume', '1.0');
        Setting::set('default_language', 'id');
        Setting::set('mute_all', '0');
    }
}
