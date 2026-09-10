<?php

namespace Tests\Feature;

use App\Models\Sound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BellApplicationFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_dashboard_page_loads_successfully(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Status Operasional Sekolah');
        $response->assertSee('Alarm Berikutnya');
    }

    public function test_api_status_returns_valid_json(): void
    {
        $response = $this->get(route('api.status'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'server_time',
            'server_date',
            'date_formatted',
            'status_type',
            'status_message',
            'schedules',
            'is_muted',
            'master_volume',
        ]);
    }

    public function test_manual_bell_trigger_api(): void
    {
        $sound = Sound::first();
        $response = $this->postJson(route('api.trigger-manual'), [
            'sound_id' => $sound->id,
            'label' => 'Uji Coba Manual Bell Test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $this->assertDatabaseHas('bell_logs', [
            'label' => 'Uji Coba Manual Bell Test',
            'source_type' => 'manual',
        ]);
    }

    public function test_mute_mode_toggle(): void
    {
        $response = $this->postJson(route('api.toggle-mute'));
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    public function test_schedules_page_loads(): void
    {
        $response = $this->get(route('schedules.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Bel Reguler');
    }

    public function test_holidays_page_loads(): void
    {
        $response = $this->get(route('holidays.index'));
        $response->assertStatus(200);
        $response->assertSee('Hari Libur Sekolah');
    }

    public function test_special_schedules_page_loads(): void
    {
        $response = $this->get(route('special.index'));
        $response->assertStatus(200);
        $response->assertSee('Jadwal Khusus');
    }

    public function test_sounds_page_loads(): void
    {
        $response = $this->get(route('sounds.index'));
        $response->assertStatus(200);
        $response->assertSee('Koleksi Suara');
    }

    public function test_settings_page_loads(): void
    {
        $response = $this->get(route('settings.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem');
    }

    public function test_logs_page_loads(): void
    {
        $response = $this->get(route('logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Riwayat');
        $response->assertSee('Log Bunyi Bel', false);
    }
}
