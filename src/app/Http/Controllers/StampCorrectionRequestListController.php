<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceCorrectionRequestStatus;
use App\Models\AttendanceCorrectionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StampCorrectionRequestListController extends Controller
{
    public function index(Request $request): View
    {
        $status = $this->resolveStatus($request->query('status'));

        $requests = AttendanceCorrectionRequest::query()
            ->where('status', $status)
            ->whereHas('attendance', fn ($query) => $query->where('user_id', $request->user()->id))
            ->with(['attendance.user'])
            ->orderByDesc('created_at')
            ->get();

        return view('stamp_correction_request.list', [
            'status' => $status,
            'requests' => $requests,
        ]);
    }

    private function resolveStatus(?string $status): AttendanceCorrectionRequestStatus
    {
        return match ($status) {
            'approved' => AttendanceCorrectionRequestStatus::Approved,
            default => AttendanceCorrectionRequestStatus::Pending,
        };
    }
}
