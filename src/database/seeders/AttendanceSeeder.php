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
        $today = Carbon::today('Asia/Tokyo');

        foreach ($users as $user) {
            for ($monthOffset = 0; $monthOffset < 6; $monthOffset++) {
                $monthStart = $today->copy()->subMonths($monthOffset)->startOfMonth();
                $monthEnd = $monthStart->copy()->endOfMonth();

                for ($date = $monthStart->copy(); $date->lte($monthEnd); $date->addDay()) {
                    if ($date->isWeekend() || $date->isAfter($today)) {
                        continue;
                    }

                    Attendance::query()->updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'work_date' => $date->toDateString(),
                        ],
                        [
                            'clock_in' => $date->copy()->setTime(9, 0, 0),
                            'clock_out' => $date->copy()->setTime(18, 0, 0),
                        ]
                    );
                }
            }
        }
    }
}
