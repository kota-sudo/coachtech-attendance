<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminStaffAttendanceController extends Controller
{
    public function show(User $user): View
    {
        abort_if($user->is_admin, 404);

        return view('admin.attendance.staff', [
            'user' => $user,
        ]);
    }
}
