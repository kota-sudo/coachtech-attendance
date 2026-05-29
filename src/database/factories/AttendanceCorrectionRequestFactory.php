<?php

namespace Database\Factories;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\Attendance;
use App\Models\AttendanceCorrectionRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceCorrectionRequest>
 */
class AttendanceCorrectionRequestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'attendance_id' => Attendance::factory(),
            'requested_clock_in' => fake()->dateTimeBetween('-1 month', 'now'),
            'requested_clock_out' => fake()->dateTimeBetween('-1 month', 'now'),
            'requested_note' => fake()->sentence(),
            'status' => AttendanceCorrectionRequestStatus::Pending,
            'approved_by' => null,
            'approved_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => AttendanceCorrectionRequestStatus::Pending,
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function approved(?User $approver = null): static
    {
        return $this->state(function (array $attributes) use ($approver) {
            $admin = $approver ?? User::factory()->admin()->create();

            return [
                'status' => AttendanceCorrectionRequestStatus::Approved,
                'approved_by' => $admin->id,
                'approved_at' => now(),
            ];
        });
    }
}
