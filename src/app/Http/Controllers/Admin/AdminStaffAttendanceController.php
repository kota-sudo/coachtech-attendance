<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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

    public function csv(Request $request, User $user): Response
    {
        abort_if($user->is_admin, 404);

        $monthParam = $request->query('month');
        $month = $this->attendanceService->parseMonth($monthParam)->format('Y-m');
        $listData = $this->attendanceService->buildMonthlyList($user, $monthParam);
        $csvContent = $this->buildCsvContent($listData['rows']);
        $filename = sprintf('attendance_%s_%s.csv', $this->sanitizeFilename($user->name), $month);

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @param  list<array{
     *     date_label: string,
     *     clock_in: ?string,
     *     clock_out: ?string,
     *     break_total: ?string,
     *     work_total: ?string,
     *     attendance_id: ?int
     * }>  $rows
     */
    private function buildCsvContent(array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            return '';
        }

        fwrite($handle, "\xEF\xBB\xBF");
        fputcsv($handle, ['日付', '出勤', '退勤', '休憩', '合計']);

        foreach ($rows as $row) {
            fputcsv($handle, [
                $row['date_label'],
                $row['clock_in'] ?? '',
                $row['clock_out'] ?? '',
                $row['break_total'] ?? '',
                $row['work_total'] ?? '',
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content === false ? '' : $content;
    }

    private function sanitizeFilename(string $name): string
    {
        $sanitized = preg_replace('/[\\\\\\/:*?"<>|]/u', '_', $name);

        return $sanitized === '' || $sanitized === null ? 'staff' : $sanitized;
    }
}
