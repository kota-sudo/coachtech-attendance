<?php

namespace Tests\Feature\Admin;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTestCaseCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_daily_list_can_navigate_next_day(): void
    {
        $admin = User::factory()->admin()->create();
        $nextDate = Carbon::parse('2026-05-30', 'Asia/Tokyo')->format('Y-m-d');

        $response = $this->actingAs($admin)->get(route('admin.attendance.list', ['date' => $nextDate]));

        $response->assertOk();
        $response->assertSee('2026年5月30日', false);
    }

    public function test_admin_can_view_approved_correction_requests(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $attendance = Attendance::factory()->forUser($staff)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '管理者承認済み確認',
            'status' => AttendanceCorrectionRequestStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('stamp_correction_request.list', ['status' => 'approved']));

        $response->assertSee('管理者承認済み確認');
        $response->assertSee('承認済み');
    }

    public function test_admin_staff_monthly_list_can_go_to_next_month(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $next = now('Asia/Tokyo')->addMonth()->format('Y-m');

        $response = $this->actingAs($admin)->get(
            route('admin.attendance.staff.show', ['user' => $staff->id, 'month' => $next])
        );

        $response->assertOk();
        $response->assertSee(now('Asia/Tokyo')->addMonth()->format('Y年'), false);
    }

    public function test_admin_update_rejects_break_end_after_clock_out(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $attendance = Attendance::factory()->forUser($staff)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '休憩修正',
            'breaks' => [
                ['break_start' => '12:00', 'break_end' => '19:00'],
            ],
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }
}
