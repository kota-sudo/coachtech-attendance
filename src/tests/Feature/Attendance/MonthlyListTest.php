<?php

namespace Tests\Feature\Attendance;

use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_navigate_multiple_previous_months(): void
    {
        $user = User::factory()->create();
        $service = app(AttendanceService::class);
        $month = null;

        $labels = [];
        for ($i = 0; $i < 6; $i++) {
            $data = $service->buildMonthlyList($user, $month);
            $labels[] = $data['monthLabel'];
            $month = $data['prevMonth'];
        }

        $this->assertCount(6, array_unique($labels));
    }

    public function test_attendance_list_shows_current_month(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance.list'));

        $response->assertOk();
        $response->assertSee(now('Asia/Tokyo')->format('Y/m'), false);
    }

    public function test_attendance_list_can_go_to_previous_month(): void
    {
        $user = User::factory()->create();
        $prev = now('Asia/Tokyo')->subMonth()->format('Y-m');

        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => $prev]));

        $response->assertOk();
        $response->assertSee(now('Asia/Tokyo')->subMonth()->format('Y/m'), false);
    }

    public function test_clock_in_time_appears_on_monthly_list(): void
    {
        $user = User::factory()->create();
        $service = app(AttendanceService::class);
        \Carbon\Carbon::setTestNow(\Carbon\Carbon::parse('2026-05-29 10:15:00', 'Asia/Tokyo'));

        $service->clockIn($user);
        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => '2026-05']));

        $response->assertSee('10:15');
        \Carbon\Carbon::setTestNow();
    }
}
