<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BreakTime>
 */
class BreakTimeFactory extends Factory
{
    public function definition(): array
    {
        $breakStart = fake()->dateTimeBetween('-1 month', 'now');
        $breakEnd = (clone $breakStart)->modify('+1 hour');

        return [
            'attendance_id' => Attendance::factory(),
            'break_start' => $breakStart,
            'break_end' => $breakEnd,
        ];
    }

    public function forAttendance(Attendance $attendance): static
    {
        return $this->state(fn (array $attributes) => [
            'attendance_id' => $attendance->id,
        ]);
    }
}
