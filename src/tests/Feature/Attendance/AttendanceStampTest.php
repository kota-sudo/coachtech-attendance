<?php

namespace Tests\Feature\Attendance;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceStampTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-05-29 09:00:00', 'Asia/Tokyo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_clock_in_button_is_shown_when_off_duty(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertOk();
        $response->assertSee('勤務外');
        $response->assertSee('出勤', false);
    }

    public function test_status_becomes_working_after_clock_in(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('出勤中');
        $this->assertTrue(
            Attendance::where('user_id', $user->id)
                ->whereDate('work_date', '2026-05-29')
                ->exists()
        );
    }

    public function test_status_becomes_breaking_after_break_in(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.break-in'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('休憩中');
    }

    public function test_status_becomes_working_after_break_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.break-in'));
        $this->actingAs($user)->post(route('attendance.break-out'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('出勤中');
    }

    public function test_status_becomes_completed_after_clock_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.clock-out'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('退勤済');
    }

    public function test_thank_you_message_is_shown_after_clock_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.clock-out'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('お疲れ様でした。');
    }

    public function test_clock_in_is_allowed_only_once_per_day(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.clock-in'));

        $this->assertSame(1, Attendance::where('user_id', $user->id)->count());

        $this->actingAs($user)->post(route('attendance.clock-out'));
        $this->actingAs($user)->post(route('attendance.clock-in'));

        $this->assertSame(1, Attendance::where('user_id', $user->id)->count());
    }
}
