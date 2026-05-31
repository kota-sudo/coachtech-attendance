<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-05-29 10:30:00', 'Asia/Tokyo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_stamp_page_shows_date_and_status(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertOk();
        $response->assertSee('2026年5月29日', false);
        $response->assertSee('勤務外');
    }

    public function test_stamp_page_shows_live_clock_element(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('id="current-time"', false);
    }

    public function test_attendance_detail_shows_record_information(): void
    {
        $user = User::factory()->create(['name' => 'テスト太郎']);
        $workDate = Carbon::parse('2026-05-20', 'Asia/Tokyo');
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => $workDate->toDateString(),
            'clock_in' => $workDate->copy()->setTime(9, 0),
            'clock_out' => $workDate->copy()->setTime(18, 0),
            'note' => 'テスト備考',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.detail', $attendance));

        $response->assertOk();
        $response->assertSee('テスト太郎');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('テスト備考');
    }

    public function test_clock_out_time_appears_on_monthly_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        Carbon::setTestNow(Carbon::parse('2026-05-29 18:30:00', 'Asia/Tokyo'));
        $this->actingAs($user)->post(route('attendance.clock-out'));

        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => '2026-05']));

        $response->assertSee('10:30');
        $response->assertSee('18:30');
    }

    public function test_break_time_appears_on_monthly_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        Carbon::setTestNow(Carbon::parse('2026-05-29 12:00:00', 'Asia/Tokyo'));
        $this->actingAs($user)->post(route('attendance.break-in'));
        Carbon::setTestNow(Carbon::parse('2026-05-29 13:00:00', 'Asia/Tokyo'));
        $this->actingAs($user)->post(route('attendance.break-out'));

        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => '2026-05']));

        $response->assertSee('1:00');
    }
}
