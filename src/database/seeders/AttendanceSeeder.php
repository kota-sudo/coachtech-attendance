<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::query()->where('is_admin', false)->get();

        foreach ($users as $user) {
            for ($day = 14; $day >= 1; $day--) {
                $date = Carbon::today()->subDays($day);

                if ($date->isWeekend()) {
                    continue;
                }

                $clockIn = $date->copy()->setTime(9, 0, 0);
                $clockOut = $date->copy()->setTime(18, 0, 0);

                Attendance::factory()->forUser($user)->create([
                    'work_date' => $date->toDateString(),
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                ]);
            }
        }
    }
}
