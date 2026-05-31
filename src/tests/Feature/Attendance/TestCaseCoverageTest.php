<?php

namespace Tests\Feature\Attendance;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestCaseCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_attendance_list_can_go_to_next_month(): void
    {
        $user = User::factory()->create();
        $next = now('Asia/Tokyo')->addMonth()->format('Y-m');

        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => $next]));

        $response->assertOk();
        $response->assertSee(now('Asia/Tokyo')->addMonth()->format('Y/m'), false);
    }

    public function test_attendance_list_detail_link_navigates_to_detail_page(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        $response = $this->actingAs($user)->get(route('attendance.list', ['month' => '2026-05']));

        $response->assertSee(route('attendance.detail', $attendance, false));
    }

    public function test_attendance_detail_shows_break_times(): void
    {
        $user = User::factory()->create(['name' => '休憩確認']);
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        BreakTime::factory()->forAttendance($attendance)->create([
            'break_start' => '2026-05-20 12:00:00',
            'break_end' => '2026-05-20 13:00:00',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.detail', $attendance));

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }

    public function test_correction_rejects_break_end_after_clock_out(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-29 12:00:00', 'Asia/Tokyo'));

        $user = User::factory()->create();
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_note' => '休憩修正',
            'breaks' => [
                ['break_start' => '12:00', 'break_end' => '19:00'],
            ],
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_end' => '休憩時間もしくは退勤時間が不適切な値です']);

        Carbon::setTestNow();
    }

    public function test_user_can_view_pending_correction_requests(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-21',
            'clock_in' => Carbon::parse('2026-05-21 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-21 18:00:00', 'Asia/Tokyo'),
        ]);

        AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '承認待ちテスト',
        ]);

        $response = $this->actingAs($user)->get(route('stamp_correction_request.list', ['status' => 'pending']));

        $response->assertSee('承認待ちテスト');
        $response->assertSee('承認待ち');
    }

    public function test_correction_list_detail_link_navigates_to_attendance_detail(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-22',
            'clock_in' => Carbon::parse('2026-05-22 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-22 18:00:00', 'Asia/Tokyo'),
        ]);

        AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '詳細リンク確認',
        ]);

        $response = $this->actingAs($user)->get(route('stamp_correction_request.list', ['status' => 'pending']));

        $response->assertSee(route('attendance.detail', $attendance, false));
    }

    public function test_stamp_page_shows_off_duty_status(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('attendance'))
            ->assertSee('勤務外');
    }

    public function test_completed_status_user_cannot_clock_in_again(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-29 09:00:00', 'Asia/Tokyo'));

        $user = User::factory()->create();
        $this->actingAs($user)->post(route('attendance.clock-in'));
        $this->actingAs($user)->post(route('attendance.clock-out'));

        $response = $this->actingAs($user)->get(route('attendance'));

        $response->assertSee('退勤済');
        $response->assertDontSee('>出勤</button>', false);

        Carbon::setTestNow();
    }
}
