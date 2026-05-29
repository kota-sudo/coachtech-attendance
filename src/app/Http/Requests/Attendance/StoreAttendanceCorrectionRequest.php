<?php

namespace App\Http\Requests\Attendance;

use App\Models\Attendance;
use App\Services\AttendanceCorrectionService;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreAttendanceCorrectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Attendance $attendance */
        $attendance = $this->route('attendance');

        return $this->user() !== null
            && $attendance->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'requested_clock_in' => ['required', 'date_format:H:i'],
            'requested_clock_out' => ['required', 'date_format:H:i'],
            'requested_note' => ['required', 'string'],
            'breaks' => ['nullable', 'array'],
            'breaks.*.break_start' => ['nullable', 'date_format:H:i'],
            'breaks.*.break_end' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'requested_note.required' => '備考を記入してください',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            /** @var Attendance $attendance */
            $attendance = $this->route('attendance');
            $service = app(AttendanceCorrectionService::class);
            $workDate = $attendance->work_date->copy()->timezone('Asia/Tokyo');

            try {
                $clockIn = $service->combineDateAndTime($workDate, $this->input('requested_clock_in'));
                $clockOut = $service->combineDateAndTime($workDate, $this->input('requested_clock_out'));
            } catch (\Exception) {
                $validator->errors()->add('requested_clock_in', '出勤時間もしくは退勤時間が不適切な値です');

                return;
            }

            if ($clockIn->gte($clockOut)) {
                $validator->errors()->add('requested_clock_in', '出勤時間もしくは退勤時間が不適切な値です');
                $validator->errors()->add('requested_clock_out', '出勤時間もしくは退勤時間が不適切な値です');
            }

            foreach ($this->input('breaks', []) as $index => $break) {
                $startTime = $break['break_start'] ?? null;
                $endTime = $break['break_end'] ?? null;

                if ($startTime === null && $endTime === null) {
                    continue;
                }

                if ($startTime === null || $endTime === null) {
                    $validator->errors()->add("breaks.{$index}.break_start", '休憩時間が不適切な値です');

                    continue;
                }

                try {
                    $breakStart = $service->combineDateAndTime($workDate, $startTime);
                    $breakEnd = $service->combineDateAndTime($workDate, $endTime);
                } catch (\Exception) {
                    $validator->errors()->add("breaks.{$index}.break_start", '休憩時間が不適切な値です');

                    continue;
                }

                if ($breakStart->lt($clockIn) || $breakStart->gt($clockOut)) {
                    $validator->errors()->add("breaks.{$index}.break_start", '休憩時間が不適切な値です');
                }

                if ($breakEnd->gt($clockOut)) {
                    $validator->errors()->add("breaks.{$index}.break_end", '休憩時間もしくは退勤時間が不適切な値です');
                }

                if ($breakStart->gte($breakEnd)) {
                    $validator->errors()->add("breaks.{$index}.break_start", '休憩時間が不適切な値です');
                }
            }
        });
    }

    /**
     * @return array{
     *     requested_clock_in: \Carbon\Carbon,
     *     requested_clock_out: \Carbon\Carbon,
     *     requested_note: string,
     *     breaks: list<array{break_start: \Carbon\Carbon, break_end: \Carbon\Carbon}>
     * }
     */
    public function validatedCorrectionData(): array
    {
        /** @var Attendance $attendance */
        $attendance = $this->route('attendance');
        $service = app(AttendanceCorrectionService::class);
        $workDate = $attendance->work_date->copy()->timezone('Asia/Tokyo');

        $clockIn = $service->combineDateAndTime($workDate, $this->input('requested_clock_in'));
        $clockOut = $service->combineDateAndTime($workDate, $this->input('requested_clock_out'));

        $breaks = [];

        foreach ($this->input('breaks', []) as $break) {
            $startTime = $break['break_start'] ?? null;
            $endTime = $break['break_end'] ?? null;

            if ($startTime === null && $endTime === null) {
                continue;
            }

            $breaks[] = [
                'break_start' => $service->combineDateAndTime($workDate, $startTime),
                'break_end' => $service->combineDateAndTime($workDate, $endTime),
            ];
        }

        return [
            'requested_clock_in' => $clockIn,
            'requested_clock_out' => $clockOut,
            'requested_note' => $this->input('requested_note'),
            'breaks' => $breaks,
        ];
    }
}
