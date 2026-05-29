<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\AttendanceCorrectionRequest;
use App\Services\AttendanceCorrectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StampCorrectionRequestApproveController extends Controller
{
    public function __construct(
        private readonly AttendanceCorrectionService $correctionService,
    ) {}

    public function show(AttendanceCorrectionRequest $attendanceCorrectionRequest): View
    {
        $attendanceCorrectionRequest->load([
            'attendance.user',
            'correctionBreaks',
            'approver',
        ]);

        return view('stamp_correction_request.approve', [
            'correctionRequest' => $attendanceCorrectionRequest,
            'isPending' => $attendanceCorrectionRequest->status === AttendanceCorrectionRequestStatus::Pending,
        ]);
    }

    public function store(Request $request, AttendanceCorrectionRequest $attendanceCorrectionRequest): RedirectResponse
    {
        if ($attendanceCorrectionRequest->status !== AttendanceCorrectionRequestStatus::Pending) {
            return redirect()
                ->route('stamp_correction_request.approve.show', $attendanceCorrectionRequest)
                ->with('status', 'この申請は既に承認済みです。');
        }

        $this->correctionService->approve($attendanceCorrectionRequest, $request->user());

        return redirect()
            ->route('stamp_correction_request.list', ['status' => 'approved'])
            ->with('status', '修正申請を承認しました。');
    }
}
