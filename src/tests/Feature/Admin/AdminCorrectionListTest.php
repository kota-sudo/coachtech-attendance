<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCorrectionListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_pending_correction_request_list(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['name' => '申請ユーザー']);
        $attendance = Attendance::factory()->forUser($staff)->create([
            'work_date' => '2026-05-20',
            'clock_in' => Carbon::parse('2026-05-20 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-20 18:00:00', 'Asia/Tokyo'),
        ]);

        AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '打刻漏れの修正依頼',
        ]);

        $response = $this->actingAs($admin)->get(route('stamp_correction_request.list', ['status' => 'pending']));

        $response->assertOk();
        $response->assertSee('申請ユーザー');
        $response->assertSee('打刻漏れの修正依頼');
        $response->assertSee('承認待ち');
    }

    public function test_admin_can_view_correction_request_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['name' => '詳細確認ユーザー']);
        $attendance = Attendance::factory()->forUser($staff)->create([
            'work_date' => '2026-05-21',
            'clock_in' => Carbon::parse('2026-05-21 09:00:00', 'Asia/Tokyo'),
            'clock_out' => Carbon::parse('2026-05-21 18:00:00', 'Asia/Tokyo'),
        ]);

        $correctionRequest = AttendanceCorrectionRequest::factory()->pending()->create([
            'attendance_id' => $attendance->id,
            'requested_note' => '詳細画面確認用',
        ]);

        $response = $this->actingAs($admin)->get(
            route('stamp_correction_request.approve.show', $correctionRequest)
        );

        $response->assertOk();
        $response->assertSee('詳細確認ユーザー');
        $response->assertSee('詳細画面確認用');
    }

    public function test_admin_daily_list_can_navigate_previous_day(): void
    {
        $admin = User::factory()->admin()->create();
        $prevDate = Carbon::parse('2026-05-28', 'Asia/Tokyo')->format('Y-m-d');

        $response = $this->actingAs($admin)->get(route('admin.attendance.list', ['date' => $prevDate]));

        $response->assertOk();
        $response->assertSee('2026年5月28日', false);
    }
}
