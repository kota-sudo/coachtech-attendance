<?php

namespace Database\Seeders;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionBreak;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class AttendanceCorrectionRequestSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('is_admin', true)->first();
        $attendances = Attendance::query()->limit(3)->get();

        if ($attendances->isEmpty() || ! $admin) {
            return;
        }

        $pendingAttendance = $attendances[0];
        $pendingRequest = AttendanceCorrectionRequest::factory()
            ->pending()
            ->create([
                'attendance_id' => $pendingAttendance->id,
                'requested_clock_in' => $pendingAttendance->clock_in->copy()->setTime(8, 55, 0),
                'requested_clock_out' => $pendingAttendance->clock_out?->copy()->setTime(18, 30, 0),
                'requested_note' => '出勤・退勤時刻の打刻漏れのため修正をお願いします。',
            ]);

        AttendanceCorrectionBreak::factory()
            ->forCorrectionRequest($pendingRequest)
            ->create([
                'break_start' => $pendingAttendance->work_date->format('Y-m-d').' 12:00:00',
                'break_end' => $pendingAttendance->work_date->format('Y-m-d').' 12:45:00',
            ]);

        if ($attendances->count() < 2) {
            return;
        }

        $approvedAttendance = $attendances[1];
        $approvedRequest = AttendanceCorrectionRequest::factory()->create([
            'attendance_id' => $approvedAttendance->id,
            'requested_clock_in' => $approvedAttendance->clock_in->copy()->setTime(9, 5, 0),
            'requested_clock_out' => $approvedAttendance->clock_out?->copy()->setTime(18, 0, 0),
            'requested_note' => '退勤打刻の修正申請です。',
            'status' => AttendanceCorrectionRequestStatus::Approved,
            'approved_by' => $admin->id,
            'approved_at' => now(),
        ]);

        AttendanceCorrectionBreak::factory()
            ->forCorrectionRequest($approvedRequest)
            ->create([
                'break_start' => $approvedAttendance->work_date->format('Y-m-d').' 12:00:00',
                'break_end' => $approvedAttendance->work_date->format('Y-m-d').' 13:00:00',
            ]);
    }
}
