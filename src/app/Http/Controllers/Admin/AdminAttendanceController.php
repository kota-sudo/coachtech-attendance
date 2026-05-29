<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function show(Attendance $attendance): View
    {
        $attendance->load(['user', 'breakTimes']);

        return view('admin.attendance.show', [
            'attendance' => $attendance,
        ]);
    }
}
