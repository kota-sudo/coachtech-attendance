<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceListController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        $listData = $this->attendanceService->buildMonthlyList(
            $request->user(),
            $request->query('month'),
        );

        return view('attendance.list', $listData);
    }

    public function detail(Request $request, Attendance $attendance): View
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);

        return view('attendance.detail', [
            'attendance' => $attendance->load('breakTimes'),
        ]);
    }
}
