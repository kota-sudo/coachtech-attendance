<?php

namespace App\Services;

use App\Enums\AttendanceStatus;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;
class AttendanceService
{
    private const TIMEZONE = 'Asia/Tokyo';

    public function today(): Carbon
    {
        return Carbon::today(self::TIMEZONE);
    }

    public function now(): Carbon
    {
        return Carbon::now(self::TIMEZONE);
    }

    public function findTodayAttendance(User $user): ?Attendance
    {
        return Attendance::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', $this->today())
            ->with(['breakTimes' => fn ($query) => $query->orderByDesc('id')])
            ->first();
    }

    public function resolveStatus(?Attendance $attendance): AttendanceStatus
    {
        if ($attendance === null || $attendance->clock_in === null) {
            return AttendanceStatus::OffDuty;
        }

        if ($attendance->clock_out !== null) {
            return AttendanceStatus::Completed;
        }

        if ($attendance->hasOpenBreak()) {
            return AttendanceStatus::Breaking;
        }

        return AttendanceStatus::Working;
    }

    public function clockIn(User $user): void
    {
        if ($this->resolveStatus($this->findTodayAttendance($user)) !== AttendanceStatus::OffDuty) {
            return;
        }

        Attendance::create([
            'user_id' => $user->id,
            'work_date' => $this->today()->toDateString(),
            'clock_in' => $this->now(),
        ]);
    }

    public function breakIn(User $user): void
    {
        $attendance = $this->findTodayAttendance($user);

        if ($this->resolveStatus($attendance) !== AttendanceStatus::Working) {
            return;
        }

        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => $this->now(),
        ]);
    }

    public function breakOut(User $user): void
    {
        $attendance = $this->findTodayAttendance($user);

        if ($this->resolveStatus($attendance) !== AttendanceStatus::Breaking) {
            return;
        }

        $openBreak = $attendance->openBreak();

        if ($openBreak === null) {
            return;
        }

        $openBreak->update([
            'break_end' => $this->now(),
        ]);
    }

    public function clockOut(User $user): void
    {
        $attendance = $this->findTodayAttendance($user);

        if ($this->resolveStatus($attendance) !== AttendanceStatus::Working) {
            return;
        }

        $attendance->update([
            'clock_out' => $this->now(),
        ]);
    }
}
