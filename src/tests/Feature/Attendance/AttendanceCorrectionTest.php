<?php

namespace Tests\Feature\Attendance;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceCorrectionTest extends TestCase
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

    public function test_user_can_create_correction_request_from_attendance_detail(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '09:30',
            'requested_clock_out' => '18:30',
            'requested_note' => '打刻漏れのため修正をお願いします。',
        ]);

        $response->assertRedirect(route('attendance.detail', $attendance));
        $response->assertSessionHas('status', '修正申請を送信しました。');

        $this->assertDatabaseHas('attendance_correction_requests', [
            'attendance_id' => $attendance->id,
            'requested_note' => '打刻漏れのため修正をお願いします。',
            'status' => AttendanceCorrectionRequestStatus::Pending->value,
        ]);
    }

    public function test_correction_request_requires_note(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '09:30',
            'requested_clock_out' => '18:30',
            'requested_note' => '',
        ]);

        $response->assertSessionHasErrors(['requested_note' => '備考を記入してください']);
    }

    public function test_pending_correction_request_prevents_new_correction_on_detail_page(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
        ]);

        $response = $this->actingAs($user)->get(route('attendance.detail', $attendance));

        $response->assertSee('承認待ちのため修正はできません。');
    }

    public function test_pending_correction_request_prevents_submitting_new_correction(): void
    {
        $user = User::factory()->create();
        $attendance = $this->createAttendanceForUser($user);

        AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
        ]);

        $response = $this->actingAs($user)->post(route('attendance.detail.store', $attendance), [
            'requested_clock_in' => '09:30',
            'requested_clock_out' => '18:30',
            'requested_note' => '再度申請',
        ]);

        $response->assertSessionHasErrors(['form' => '承認待ちのため修正はできません。']);
        $this->assertSame(1, AttendanceCorrectionRequest::where('attendance_id', $attendance->id)->count());
    }

    private function createAttendanceForUser(User $user): Attendance
    {
        return Attendance::factory()->forUser($user)->create([
            'work_date' => '2026-05-28',
            'clock_in' => Carbon::parse('2026-05-28 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-28 18:00:00', 'Asia/Tokyo'),
        ]);
    }
}
