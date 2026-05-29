<?php

use App\Http\Controllers\Admin\AdminAttendanceController;
use App\Http\Controllers\Admin\AdminAttendanceListController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceListController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\StampCorrectionRequestListController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store']);
});

Route::middleware(['auth', 'user'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn'])->name('attendance.break-in');
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut'])->name('attendance.break-out');
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

    Route::get('/attendance/list', [AttendanceListController::class, 'index'])->name('attendance.list');
    Route::get('/attendance/detail/{attendance}', [AttendanceListController::class, 'detail'])->name('attendance.detail');
    Route::post('/attendance/detail/{attendance}', [AttendanceListController::class, 'store'])->name('attendance.detail.store');

    Route::get('/stamp_correction_request/list', [StampCorrectionRequestListController::class, 'index'])
        ->name('stamp_correction_request.list');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/attendance/list', [AdminAttendanceListController::class, 'index'])
        ->name('admin.attendance.list');
    Route::get('/admin/attendance/{attendance}', [AdminAttendanceController::class, 'show'])
        ->name('admin.attendance.show');
    Route::post('/admin/attendance/{attendance}', [AdminAttendanceController::class, 'update'])
        ->name('admin.attendance.update');

    Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
});
