<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminStaffAttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function show(Request $request, User $user): View
    {
        abort_if($user->is_admin, 404);

        $monthParam = $request->query('month');
        $listData = $this->attendanceService->buildMonthlyList($user, $monthParam);
        $currentMonth = $this->attendanceService->parseMonth($monthParam)->format('Y-m');

        return view('admin.attendance.staff', array_merge($listData, [
            'user' => $user,
            'currentMonth' => $currentMonth,
        ]));
    }

    public function csv(Request $request, User $user): View
    {
        abort_if($user->is_admin, 404);

        return view('admin.attendance.staff-csv-placeholder', [
            'user' => $user,
            'month' => $request->query('month'),
        ]);
    }
}
