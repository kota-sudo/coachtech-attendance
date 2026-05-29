<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-1 month', 'now');
        $workDate = $date->format('Y-m-d');
        $clockIn = \Carbon\Carbon::parse($workDate)->setTime(9, 0, 0);
        $clockOut = (clone $clockIn)->addHours(8);

        return [
            'user_id' => User::factory(),
            'work_date' => $workDate,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function withoutClockOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'clock_out' => null,
        ]);
    }
}
