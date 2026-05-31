<?php

namespace Tests\Feature\Admin;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_update_requires_note(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $attendance = $this->createAttendance($staff);

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance), [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note' => '備考を記入してください']);
    }

    public function test_admin_update_rejects_invalid_clock_times(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $attendance = $this->createAttendance($staff);

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance), [
            'clock_in' => '18:00',
            'clock_out' => '09:00',
            'note' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['clock_in', 'clock_out']);
    }

    public function test_admin_can_update_attendance(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $attendance = $this->createAttendance($staff);

        $response = $this->actingAs($admin)->post(route('admin.attendance.update', $attendance), [
            'clock_in' => '08:30',
            'clock_out' => '19:00',
            'note' => '管理者による修正',
        ]);

        $response->assertRedirect(route('admin.attendance.show', $attendance));
        $attendance->refresh();
        $this->assertSame('08:30', $attendance->clock_in->timezone('Asia/Tokyo')->format('H:i'));
        $this->assertSame('19:00', $attendance->clock_out->timezone('Asia/Tokyo')->format('H:i'));
        $this->assertSame('管理者による修正', $attendance->note);
    }

    private function createAttendance(User $staff): Attendance
    {
        $workDate = Carbon::parse('2026-05-20', 'Asia/Tokyo');

        return Attendance::factory()->forUser($staff)->create([
            'work_date' => $workDate->toDateString(),
            'clock_in' => $workDate->copy()->setTime(9, 0),
            'clock_out' => $workDate->copy()->setTime(18, 0),
            'note' => '修正前',
        ]);
    }
}
