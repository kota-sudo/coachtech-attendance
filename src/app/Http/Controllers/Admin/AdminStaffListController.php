<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class AdminStaffListController extends Controller
{
    public function index(): View
    {
        $staff = User::query()
            ->where('is_admin', false)
            ->orderBy('name')
            ->get();

        return view('admin.staff.list', [
            'staff' => $staff,
        ]);
    }
}
