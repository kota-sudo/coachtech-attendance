<?php

namespace Database\Factories;

use App\Models\AttendanceCorrectionBreak;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceCorrectionBreak>
 */
class AttendanceCorrectionBreakFactory extends Factory
{
    public function definition(): array
    {
        $breakStart = fake()->dateTimeBetween('-1 month', 'now');
        $breakEnd = (clone $breakStart)->modify('+1 hour');

        return [
            'attendance_correction_request_id' => AttendanceCorrectionRequest::factory(),
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];
    }

    public function forCorrectionRequest(AttendanceCorrectionRequest $request): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_correction_request_id' => $request->id,
        ]);
    }
}
