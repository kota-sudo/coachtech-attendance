<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMonthlyListTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_staff_monthly_list_can_navigate_previous_months(): void
    {
        $staff = User::factory()->create();
        $service = app(AttendanceService::class);
        $month = null;
        $labels = [];

        for ($i = 0; $i < 6; $i++) {
            $data = $service->buildMonthlyList($staff, $month);
            $labels[] = $data['monthLabel'];
            $month = $data['prevMonth'];
        }

        $this->assertCount(6, array_unique($labels));
    }

    public function test_csv_filename_uses_staff_name(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['name' => '一般ユーザー1']);

        $response = $this->actingAs($admin)->get(
            route('admin.attendance.staff.csv', ['user' => $staff->id, 'month' => '2026-05'])
        );

        $response->assertOk();
        $this->assertStringContainsString('一般ユーザー1_2026-05.csv', $response->headers->get('content-disposition'));
    }
}
