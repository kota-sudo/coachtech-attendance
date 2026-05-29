<?php

namespace App\Services;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionBreak;
use App\Models\AttendanceCorrectionRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceCorrectionService
{
    private const TIMEZONE = 'Asia/Tokyo';

    /**
     * @param  array{
     *     requested_clock_in: \Carbon\Carbon,
     *     requested_clock_out: \Carbon\Carbon,
     *     requested_note: string,
     *     breaks: list<array{break_start: \Carbon\Carbon, break_end: \Carbon\Carbon}>
     * }  $data
     */
    public function store(Attendance $attendance, array $data): AttendanceCorrectionRequest
    {
        return DB::transaction(function () use ($attendance, $data) {
            $correctionRequest = AttendanceCorrectionRequest::create([
                'attendance_id' => $attendance->id,
                'requested_clock_in' => $data['requested_clock_in'],
                'requested_clock_out' => $data['requested_clock_out'],
                'requested_note' => $data['requested_note'],
                'status' => AttendanceCorrectionRequestStatus::Pending,
            ]);

            foreach ($data['breaks'] as $break) {
                AttendanceCorrectionBreak::create([
                    'attendance_correction_request_id' => $correctionRequest->id,
                    'break_start' => $break['break_start'],
                    'break_end' => $break['break_end'],
                ]);
            }

            return $correctionRequest;
        });
    }

    public function combineDateAndTime(Carbon $workDate, string $time): Carbon
    {
        return Carbon::parse(
            $workDate->format('Y-m-d').' '.$time,
            self::TIMEZONE
        );
    }
}
