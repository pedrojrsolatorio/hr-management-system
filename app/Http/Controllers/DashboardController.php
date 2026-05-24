<?php

namespace App\Http\Controllers;

use App\Models\{Attendance, Department, Employee, LeaveRequest, Payroll};
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_employees' => Employee::where('status', 'active')->count(),

            'on_leave_today'  => LeaveRequest::where('status', 'approved')
                ->whereDate('start_date', '<=', today())
                ->whereDate('end_date', '>=', today())
                ->count(),

            'present_today'   => Attendance::whereDate('date', today()->toDateString())
                ->whereIn('status', ['present', 'late'])
                ->count(),

            'pending_leaves'  => LeaveRequest::where('status', 'pending')->count(),
        ];

        // Monthly attendance — last 6 months, using whereBetween
        $attendanceData = collect(range(5, 0))->map(function (int $i) {
            $month = now()->subMonths($i);
            $start = $month->copy()->startOfMonth()->toDateString();
            $end   = $month->copy()->endOfMonth()->toDateString();

            return [
                'month'   => $month->format('M Y'),
                'present' => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'present')->count(),
                'absent'  => Attendance::whereBetween('date', [$start, $end])
                    ->where('status', 'absent')->count(),
            ];
        });

        // Department distribution
        $deptData = Department::withCount([
            'employees' => fn($q) => $q->where('status', 'active'),
        ])
            ->having('employees_count', '>', 0)
            ->get()
            ->map(fn($d) => ['name' => $d->name, 'count' => $d->employees_count]);

        // Payroll cost last 6 months
        $payrollData = collect(range(5, 0))->map(function (int $i) {
            $month = now()->subMonths($i)->format('Y-m');
            return [
                'month' => now()->subMonths($i)->format('M Y'),
                'total' => (float) Payroll::where('month', $month)->sum('net_salary'),
            ];
        });

        return view('dashboard', compact('stats', 'attendanceData', 'deptData', 'payrollData'));
    }
}
