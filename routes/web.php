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

/*
|-------------------------
| Root redirect
|-------------------------
*/

// // OPTION A — Skip welcome page, go straight to login (recommended for internal systems)
// Route::get('/', function () {
//     return auth()->check()
//         ? redirect()->route('dashboard')
//         : redirect()->route('login');
// });

// OPTION B — Show a welcome page first (better for public-facing SaaS products)
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|-------------------------
| Dashboard
|-------------------------
*/
// // Transfered to 'role:admin,hr_manager' group
// Route::get('/dashboard', [DashboardController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

/*
|-------------------------
| Auth routes (Breeze)
|-------------------------
*/
require __DIR__ . '/auth.php';

/*
|-------------------------
| Authenticated routes
|-------------------------
*/
Route::middleware('auth')->group(function () {

    // Breeze profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications (all roles)
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    /*
    |-------------------------
    | HR System Routes
    |-------------------------
    */

    // Admin + HR Manager
    Route::middleware(['role:admin,hr_manager'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('verified')
            ->name('dashboard');

        Route::resource('employees', EmployeeController::class);
        Route::resource('departments', DepartmentController::class);
        Route::resource('positions', PositionController::class);

        Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');

        Route::get('leaves', [LeaveRequestController::class, 'index'])->name('leaves.index');
        Route::patch('leaves/{leave}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
        Route::patch('leaves/{leave}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');

        Route::resource('performance-reviews', PerformanceReviewController::class);

        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/employees', [ReportController::class, 'employeeReport'])->name('reports.employees');
        Route::get('reports/payroll', [ReportController::class, 'payrollReport'])->name('reports.payroll');
        Route::get('reports/attendance', [ReportController::class, 'attendanceReport'])->name('reports.attendance');
    });

    // Admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::post('payroll/generate', [PayrollController::class, 'generate'])->name('payroll.generate');
        Route::get('payroll/{payroll}/pdf', [PayrollController::class, 'pdf'])->name('payroll.pdf');
        Route::resource('payroll', PayrollController::class)->except(['create', 'edit']);

        // Employee restore and force delete (admin only)
        Route::patch('employees/{id}/restore', [EmployeeController::class, 'restore'])
            ->name('employees.restore');
        Route::delete('employees/{id}/force', [EmployeeController::class, 'forceDestroy'])
            ->name('employees.force-destroy');
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
});
