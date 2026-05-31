<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attendance\StoreAttendanceCorrectionRequest;
use App\Models\Attendance;
use App\Services\AttendanceCorrectionService;
use App\Services\AttendanceService;
use App\Support\AttendanceDetailPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceListController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly AttendanceCorrectionService $correctionService,
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

        $attendance->load(['user', 'breakTimes']);

        $breakRowCount = $attendance->breakTimes->count() + 1;
        $hasPending = $attendance->hasPendingCorrectionRequest();

        return view('attendance.detail', array_merge(
            AttendanceDetailPresenter::present($attendance),
            [
                'attendance' => $attendance,
                'breakRowCount' => $breakRowCount,
                'hasPending' => $hasPending,
                'defaultClockIn' => old('requested_clock_in', $attendance->clock_in?->timezone('Asia/Tokyo')->format('H:i')),
                'defaultClockOut' => old('requested_clock_out', $attendance->clock_out?->timezone('Asia/Tokyo')->format('H:i')),
                'defaultNote' => old('requested_note', ''),
            ],
        ));
    }

    public function store(StoreAttendanceCorrectionRequest $request, Attendance $attendance): RedirectResponse
    {
        abort_unless($attendance->user_id === $request->user()->id, 403);

        if ($attendance->hasPendingCorrectionRequest()) {
            return redirect()
                ->route('attendance.detail', $attendance)
                ->withErrors(['form' => '承認待ちのため修正はできません。']);
        }

        $this->correctionService->store($attendance, $request->validatedCorrectionData());

        return redirect()
            ->route('attendance.detail', $attendance)
            ->with('status', '修正申請を送信しました。');
    }
}
