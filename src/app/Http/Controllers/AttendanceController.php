<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceStatus;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $today = $this->attendanceService->today();
        $attendance = $this->attendanceService->findTodayAttendance($user);
        $status = $this->attendanceService->resolveStatus($attendance);

        return view('attendance.index', [
            'today' => $today,
            'todayLabel' => $today->locale('ja')->isoFormat('YYYY年M月D日(ddd)'),
            'status' => $status,
            'attendance' => $attendance,
        ]);
    }

    public function clockIn(Request $request): RedirectResponse
    {
        $this->attendanceService->clockIn($request->user());

        return redirect()->route('attendance');
    }

    public function breakIn(Request $request): RedirectResponse
    {
        $this->attendanceService->breakIn($request->user());

        return redirect()->route('attendance');
    }

    public function breakOut(Request $request): RedirectResponse
    {
        $this->attendanceService->breakOut($request->user());

        return redirect()->route('attendance');
    }

    public function clockOut(Request $request): RedirectResponse
    {
        $this->attendanceService->clockOut($request->user());

        return redirect()->route('attendance');
    }
}
