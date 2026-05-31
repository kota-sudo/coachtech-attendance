<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Services\AdminAttendanceUpdateService;
use App\Support\AttendanceDetailPresenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminAttendanceController extends Controller
{
    public function __construct(
        private readonly AdminAttendanceUpdateService $updateService,
    ) {}

    public function show(Attendance $attendance): View
    {
        $attendance->load(['user', 'breakTimes']);

        $breakRowCount = $attendance->breakTimes->count() + 1;
        $hasPending = $attendance->hasPendingCorrectionRequest();

        return view('admin.attendance.show', array_merge(
            AttendanceDetailPresenter::present($attendance),
            [
                'attendance' => $attendance,
                'breakRowCount' => $breakRowCount,
                'hasPending' => $hasPending,
                'defaultClockIn' => old('clock_in', $attendance->clock_in?->timezone('Asia/Tokyo')->format('H:i')),
                'defaultClockOut' => old('clock_out', $attendance->clock_out?->timezone('Asia/Tokyo')->format('H:i')),
                'defaultNote' => old('note', $attendance->note ?? ''),
            ],
        ));
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): RedirectResponse
    {
        if ($attendance->hasPendingCorrectionRequest()) {
            return redirect()
                ->route('admin.attendance.show', $attendance)
                ->withErrors(['form' => '承認待ちのため修正はできません。']);
        }

        $this->updateService->update($attendance, $request->validatedUpdateData());

        return redirect()
            ->route('admin.attendance.show', $attendance)
            ->with('status', '勤怠情報を更新しました。');
    }
}
