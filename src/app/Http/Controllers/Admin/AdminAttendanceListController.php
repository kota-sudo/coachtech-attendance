<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAttendanceListController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        $listData = $this->attendanceService->buildAdminDailyList(
            $request->query('date'),
        );

        return view('admin.attendance.list', $listData);
    }
}
