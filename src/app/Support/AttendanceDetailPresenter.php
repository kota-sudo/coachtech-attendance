<?php

namespace App\Support;

use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;

class AttendanceDetailPresenter
{
    public static function present(Attendance $attendance): array
    {
        $pending = $attendance->pendingCorrectionRequest();
        $pendingBreaks = [];

        if ($pending instanceof AttendanceCorrectionRequest) {
            $pending->load("correctionBreaks");

            foreach ($pending->correctionBreaks as $break) {
                $pendingBreaks[] = [
                    "start" => $break->break_start?->timezone("Asia/Tokyo")->format("H:i") ?? "",
                    "end" => $break->break_end?->timezone("Asia/Tokyo")->format("H:i") ?? "",
                ];
            }
        }

        return [
            "year" => $attendance->work_date->format("Y年"),
            "datePart" => $attendance->work_date->format("m月d日"),
            "pendingClockIn" => $pending?->requested_clock_in?->timezone("Asia/Tokyo")->format("H:i") ?? "",
            "pendingClockOut" => $pending?->requested_clock_out?->timezone("Asia/Tokyo")->format("H:i") ?? "",
            "pendingNote" => $pending?->requested_note ?? "",
            "pendingBreaks" => $pendingBreaks,
        ];
    }
}
