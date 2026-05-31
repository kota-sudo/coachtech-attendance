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

    public function parseMonth(?string $month): Carbon
    {
        if ($month !== null && preg_match('/^\d{4}-\d{2}$/', $month)) {
            return Carbon::createFromFormat('!Y-m-d', $month.'-01', self::TIMEZONE)->startOfDay();
        }

        return $this->today()->copy()->startOfMonth();
    }

    public function parseDate(?string $date): Carbon
    {
        if ($date !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return Carbon::createFromFormat('!Y-m-d', $date, self::TIMEZONE)->startOfDay();
        }

        return $this->today();
    }

    /**
     * @return array{
     *     monthLabel: string,
     *     prevMonth: string,
     *     nextMonth: string,
     *     rows: list<array{
     *         date_label: string,
     *         clock_in: ?string,
     *         clock_out: ?string,
     *         break_total: ?string,
     *         work_total: ?string,
     *         attendance_id: ?int
     *     }>
     * }
     */
    public function buildMonthlyList(User $user, ?string $monthParam): array
    {
        $monthStart = $this->parseMonth($monthParam);
        $monthEnd = $monthStart->copy()->endOfMonth();

        $attendances = Attendance::query()
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [
                $monthStart->toDateString(),
                $monthEnd->toDateString(),
            ])
            ->with('breakTimes')
            ->get()
            ->keyBy(fn (Attendance $attendance) => $attendance->work_date->format('Y-m-d'));

        $rows = [];

        for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $attendance = $attendances->get($key);

            $rows[] = array_merge(
                ['date_label' => $date->locale('ja')->isoFormat('M/D(ddd)')],
                $this->formatAttendanceSummary($attendance),
            );
        }

        return [
            'monthLabel' => $monthStart->locale('ja')->isoFormat('YYYY年M月'),
            'currentMonthDisplay' => $monthStart->format('Y/m'),
            'prevMonth' => $monthStart->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $monthStart->copy()->addMonth()->format('Y-m'),
            'canGoNextMonth' => $monthStart->copy()->startOfMonth()->lt($this->today()->copy()->startOfMonth()),
            'rows' => $rows,
        ];
    }

    /**
     * @return array{
     *     dateLabel: string,
     *     date: string,
     *     prevDate: string,
     *     nextDate: string,
     *     rows: list<array{
     *         name: string,
     *         clock_in: ?string,
     *         clock_out: ?string,
     *         break_total: ?string,
     *         work_total: ?string,
     *         attendance_id: ?int
     *     }>
     * }
     */
    public function buildAdminDailyList(?string $dateParam): array
    {
        $date = $this->parseDate($dateParam);

        $users = User::query()
            ->where('is_admin', false)
            ->orderBy('name')
            ->get();

        $attendances = Attendance::query()
            ->whereDate('work_date', $date)
            ->whereIn('user_id', $users->pluck('id'))
            ->with('breakTimes')
            ->get()
            ->keyBy('user_id');

        $rows = [];

        foreach ($users as $user) {
            $attendance = $attendances->get($user->id);

            $rows[] = array_merge(
                ['name' => $user->name],
                $this->formatAttendanceSummary($attendance),
            );
        }

        return [
            'dateLabel' => $date->locale('ja')->isoFormat('YYYY年M月D日(ddd)'),
            'date' => $date->format('Y-m-d'),
            'prevDate' => $date->copy()->subDay()->format('Y-m-d'),
            'nextDate' => $date->copy()->addDay()->format('Y-m-d'),
            'canGoNextDate' => $date->copy()->startOfDay()->lt($this->today()),
            'rows' => $rows,
        ];
    }

    /**
     * @return array{
     *     clock_in: ?string,
     *     clock_out: ?string,
     *     break_total: ?string,
     *     work_total: ?string,
     *     attendance_id: ?int
     * }
     */
    public function formatAttendanceSummary(?Attendance $attendance): array
    {
        $row = [
            'clock_in' => null,
            'clock_out' => null,
            'break_total' => null,
            'work_total' => null,
            'attendance_id' => null,
        ];

        if ($attendance === null) {
            return $row;
        }

        $row['attendance_id'] = $attendance->id;
        $row['clock_in'] = $attendance->clock_in?->timezone(self::TIMEZONE)->format('H:i');
        $row['clock_out'] = $attendance->clock_out?->timezone(self::TIMEZONE)->format('H:i');

        $breakSeconds = $this->totalBreakSeconds($attendance);

        if ($breakSeconds > 0) {
            $row['break_total'] = $this->formatDuration($breakSeconds);
        }

        if ($attendance->clock_in !== null && $attendance->clock_out !== null) {
            $workSeconds = (int) $attendance->clock_in->diffInSeconds($attendance->clock_out) - $breakSeconds;

            if ($workSeconds >= 0) {
                $row['work_total'] = $this->formatDuration($workSeconds);
            }
        }

        return $row;
    }

    private function totalBreakSeconds(Attendance $attendance): int
    {
        $total = 0;
        $capEnd = $attendance->clock_out ?? $this->now();

        foreach ($attendance->breakTimes as $break) {
            if ($break->break_start === null) {
                continue;
            }

            $end = $break->break_end ?? $capEnd;
            $total += (int) $break->break_start->diffInSeconds($end);
        }

        return $total;
    }

    private function formatDuration(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        return sprintf('%d:%02d', $hours, $minutes);
    }
}
