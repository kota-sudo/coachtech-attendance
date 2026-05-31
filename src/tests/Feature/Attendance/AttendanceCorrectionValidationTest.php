<?php

namespace Tests\Feature\Attendance;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2026-05-29 12:00:00', 'Asia/Tokyo'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_correction_rejects_clock_out_before_clock_in(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '18:00',
            'requested_clock_out' => '09:00',
            'requested_note' => '時刻修正',
        ]);

        $response->assertSessionHasErrors(['requested_clock_in', 'requested_clock_out']);
    }

    public function test_correction_rejects_invalid_break_time(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_note' => '休憩修正',
            'breaks' => [
                ['break_start' => '08:00', 'break_end' => '08:30'],
            ],
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_start']);
    }

    public function test_user_can_view_approved_correction_requests(): void
    {
        $user = User::factory()->create(['name' => '申請者']);
        $attendance = $this->createAttendanceForUser($user);
        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '承認済み申請',
            'status' => AttendanceCorrectionRequestStatus::Approved,
        ]);

        $response = $this->actingAs($user)->get(route('stamp_correction_request.list', ['status' => 'approved']));

        $response->assertOk();
        $response->assertSee('承認済み申請');
        $response->assertSee('承認済み');
    }

    private function createAttendanceForUser(User $user): Attendance
    {
        return Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);
    }
}
