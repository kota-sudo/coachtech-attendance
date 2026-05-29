<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;

class AdminAttendanceUpdateService
{
    /**
     * @param  array{
     *     clock_in: \Carbon\Carbon,
     *     clock_out: \Carbon\Carbon,
     *     note: string,
     *     breaks: list<array{break_start: \Carbon\Carbon, break_end: \Carbon\Carbon}>
     * }  $data
     */
    public function update(Attendance $attendance, array $data): void
    {
        DB::transaction(function () use ($attendance, $data) {
            $attendance->update([
                'clock_in' => $data['clock_in'],
                'clock_out' => $data['clock_out'],
                'note' => $data['note'],
            ]);

            $attendance->breakTimes()->delete();

            foreach ($data['breaks'] as $break) {
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $break['break_start'],
                    'break_end' => $break['break_end'],
                ]);
            }
        });
    }
}
