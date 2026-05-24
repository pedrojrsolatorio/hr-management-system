<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('employees', Api\EmployeeController::class);
    // Route::apiResource('attendance', Api\AttendanceController::class);
    // Route::apiResource('leaves',     Api\LeaveRequestController::class);
    // Route::apiResource('payroll',    Api\PayrollController::class);
});
