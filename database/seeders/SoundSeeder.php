<?php

namespace Database\Seeders;

use App\Models\Sound;
use Illuminate\Database\Seeder;

class SoundSeeder extends Seeder
{
    public function run(): void
    {
        $sounds = [
            // Bahasa Indonesia
            [
                'name' => 'Bel Masuk Kelas (ID)',
                'file_path' => 'audio/id/masuk.wav',
                'language' => 'id',
                'duration_sec' => 5,
                'is_system_default' => true,
            ],
            [
                'name' => 'Bel Istirahat (ID)',
                'file_path' => 'audio/id/istirahat.wav',
                'language' => 'id',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],
            [
                'name' => 'Bel Pulang Sekolah (ID)',
                'file_path' => 'audio/id/pulang.wav',
                'language' => 'id',
                'duration_sec' => 5,
                'is_system_default' => true,
            ],
            [
                'name' => 'Bel Ujian / Ujian Selesai (ID)',
                'file_path' => 'audio/id/ujian.wav',
                'language' => 'id',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],
            [
                'name' => 'Nada Chime Standar (ID)',
                'file_path' => 'audio/id/chime.wav',
                'language' => 'id',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],

            // English
            [
                'name' => 'School Bell Enter (EN)',
                'file_path' => 'audio/en/masuk.wav',
                'language' => 'en',
                'duration_sec' => 5,
                'is_system_default' => true,
            ],
            [
                'name' => 'Recess Bell (EN)',
                'file_path' => 'audio/en/istirahat.wav',
                'language' => 'en',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],
            [
                'name' => 'Dismissal / School Over (EN)',
                'file_path' => 'audio/en/pulang.wav',
                'language' => 'en',
                'duration_sec' => 5,
                'is_system_default' => true,
            ],
            [
                'name' => 'Exam Bell (EN)',
                'file_path' => 'audio/en/ujian.wav',
                'language' => 'en',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],
            [
                'name' => 'Standard Chime Tone (EN)',
                'file_path' => 'audio/en/chime.wav',
                'language' => 'en',
                'duration_sec' => 4,
                'is_system_default' => true,
            ],
        ];

        foreach ($sounds as $item) {
            Sound::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
