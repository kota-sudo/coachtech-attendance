<?php

namespace Tests\Feature\Admin;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_daily_attendance_list(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.attendance.list'));

        $response->assertOk();
        $response->assertSee('勤怠一覧（管理者）', false);
    }

    public function test_admin_can_view_staff_list(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.staff.list'));

        $response->assertOk();
        $response->assertSee('スタッフ一覧');
    }

    public function test_admin_can_approve_correction_request(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $correctionRequest = $this->createPendingCorrectionRequest($staff);

        $response = $this->actingAs($admin)->post(
            route('stamp_correction_request.approve.store', $correctionRequest)
        );

        $response->assertRedirect(route('stamp_correction_request.list', ['status' => 'approved']));

        $correctionRequest->refresh();

        $this->assertSame(AttendanceCorrectionRequestStatus::Approved, $correctionRequest->status);
        $this->assertSame($admin->id, $correctionRequest->approved_by);
        $this->assertNotNull($correctionRequest->approved_at);
    }

    public function test_approval_updates_attendance_with_requested_values(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $correctionRequest = $this->createPendingCorrectionRequest($staff);
        $attendance = $correctionRequest->attendance;

        $this->actingAs($admin)->post(
            route('stamp_correction_request.approve.store', $correctionRequest)
        );

        $attendance->refresh();
        $correctionRequest->refresh();

        $this->assertTrue($attendance->clock_in->equalTo($correctionRequest->requested_clock_in));
        $this->assertTrue($attendance->clock_out->equalTo($correctionRequest->requested_clock_out));
        $this->assertSame('修正後の備考', $attendance->note);
    }

    private function createPendingCorrectionRequest(User $staff): AttendanceCorrectionRequest
    {
        $workDate = Carbon::parse('2026-05-20', 'Asia/Tokyo');

        $attendance = Attendance::factory()->forUser($staff)->create([
            'work_date' => $workDate->toDateString(),
            'clock_in' => $workDate->copy()->setTime(9, 0),
            'clock_out' => $workDate->copy()->setTime(18, 0),
            'note' => '修正前の備考',
        ]);

        return AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
            'requested_clock_in' => $workDate->copy()->setTime(8, 30),
            'requested_clock_out' => $workDate->copy()->setTime(19, 0),
            'requested_note' => '修正後の備考',
        ]);
    }
}
