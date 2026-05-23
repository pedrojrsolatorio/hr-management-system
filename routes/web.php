<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    EmployeeController,
    DepartmentController,
    AttendanceController,
    LeaveRequestController,
    PayrollController,
    PerformanceReviewController,
    PositionController,
    ReportController,
    NotificationController,
    ProfileController
};

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

require __DIR__ . '/auth.php';

Route::middleware('auth')->group(function () {

    /*
    |-------------------------
    | Profile (Breeze)
    |-------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |-------------------------
    | HR System Routes
    |-------------------------
    */

    // Admin + HR Manager
    Route::middleware(['role:admin,hr_manager'])->group(function () {

        Route::resource('employees', EmployeeController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);

        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::patch('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::patch('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');

        Route::resource('performance-reviews', PerformanceReviewController::class);
        Route::resource('reports', ReportController::class)->only(['index', 'show']);
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {

        Route::resource('payroll', PayrollController::class);
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::get('payroll/{payroll}/pdf', [PayrollController::class, 'pdf'])->name('payroll.pdf');
    });

    // Employee routes
    Route::middleware(['role:employee'])->group(function () {

        Route::get('my-profile', [EmployeeController::class, 'profile'])->name('employee.profile');
        Route::get('my-attendance', [AttendanceController::class, 'myAttendance'])->name('attendance.my');

        Route::post('attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');

        Route::get('my-leaves', [LeaveRequestController::class, 'myLeaves'])->name('leaves.my');
        Route::post('leaves', [LeaveRequestController::class, 'store'])->name('leaves.store');

        Route::get('my-payslips', [PayrollController::class, 'myPayslips'])->name('payroll.my');
    });

    /*
    |-------------------------
    | Notifications
    |-------------------------
    */

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
});
