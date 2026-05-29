<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Database\Seeder;

class BreakTimeSeeder extends Seeder
{
    public function run(): void
    {
        Attendance::query()->each(function (Attendance $attendance) {
            $date = $attendance->work_date->format('Y-m-d');

            BreakTime::factory()->forAttendance($attendance)->create([
                'break_start' => "{$date} 12:00:00",
                'break_end' => "{$date} 13:00:00",
            ]);
        });
    }
}
