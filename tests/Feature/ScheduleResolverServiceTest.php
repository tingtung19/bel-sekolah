<?php

namespace Tests\Feature;

use App\Models\Holiday;
use App\Models\Schedule;
use App\Models\SpecialSchedule;
use App\Services\ScheduleResolverService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScheduleResolverServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_schedule_runs_on_regular_weekday(): void
    {
        // Setup jadwal Senin
        Schedule::create([
            'time' => '07:00',
            'label' => 'Masuk Reguler',
            'days_of_week' => ['Mon'],
            'language' => 'id',
            'is_active' => true,
        ]);

        $resolver = new ScheduleResolverService();
        // 2026-09-07 adalah hari Senin (Mon)
        $monday = Carbon::parse('2026-09-07');

        $result = $resolver->getActiveSchedulesForToday($monday);

        $this->assertEquals('normal', $result['status_type']);
        $this->assertCount(1, $result['schedules']);
        $this->assertEquals('Masuk Reguler', $result['schedules'][0]['label']);
    }

    public function test_holiday_skips_regular_schedule(): void
    {
        Schedule::create([
            'time' => '07:00',
            'label' => 'Masuk Reguler',
            'days_of_week' => ['Mon'],
            'language' => 'id',
            'is_active' => true,
        ]);

        Holiday::create([
            'date' => '2026-09-07',
            'is_recurring_weekly' => false,
            'description' => 'Libur Maulid Nabi',
        ]);

        $resolver = new ScheduleResolverService();
        $monday = Carbon::parse('2026-09-07');

        $result = $resolver->getActiveSchedulesForToday($monday);

        $this->assertEquals('holiday', $result['status_type']);
        $this->assertEmpty($result['schedules']);
    }

    public function test_special_schedule_overrides_regular_schedule(): void
    {
        Schedule::create([
            'time' => '07:00',
            'label' => 'Masuk Reguler',
            'days_of_week' => ['Mon'],
            'language' => 'id',
            'is_active' => true,
        ]);

        SpecialSchedule::create([
            'date' => '2026-09-07',
            'time' => '08:00',
            'label' => 'Ujian Akhir Semester',
            'language' => 'id',
            'overrides_regular' => true,
        ]);

        $resolver = new ScheduleResolverService();
        $monday = Carbon::parse('2026-09-07');

        $result = $resolver->getActiveSchedulesForToday($monday);

        $this->assertEquals('special', $result['status_type']);
        $this->assertCount(1, $result['schedules']);
        $this->assertEquals('Ujian Akhir Semester', $result['schedules'][0]['label']);
        $this->assertEquals('08:00', $result['schedules'][0]['time']);
    }
}
