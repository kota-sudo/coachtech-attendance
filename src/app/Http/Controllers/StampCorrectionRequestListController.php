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
        $user = $request->user();
        $isAdmin = $user->isAdmin();
        $status = $this->resolveStatus($request->query('status'));

        $query = AttendanceCorrectionRequest::query()
            ->where('status', $status)
            ->with(['attendance.user'])
            ->orderByDesc('created_at');

        if (! $isAdmin) {
            $query->whereHas('attendance', fn ($q) => $q->where('user_id', $user->id));
        }

        $view = $isAdmin
            ? 'admin.stamp_correction_request.list'
            : 'stamp_correction_request.list';

        return view($view, [
            'status' => $status,
            'requests' => $query->get(),
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
