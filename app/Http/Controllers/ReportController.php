<?php

namespace App\Http\Controllers;

use App\Models\{Employee, Payroll, Attendance, LeaveRequest};
use App\Exports\{EmployeesExport, PayrollExport};
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function employeeReport(Request $request): mixed
    {
        $employees = Employee::with(['user', 'department', 'position'])
            ->where('status', 'active')
            ->get();

        if ($request->format === 'pdf') {
            return Pdf::loadView('reports.employees-pdf', compact('employees'))
                ->stream('employee-report.pdf');
        }

        if ($request->format === 'excel') {
            return Excel::download(new EmployeesExport, 'employees.xlsx');
        }

        return view('reports.employees', compact('employees'));
    }

    public function payrollReport(Request $request): mixed
    {
        $month    = $request->get('month', now()->format('Y-m'));
        $payrolls = Payroll::with(['employee.user', 'employee.department'])
            ->where('month', $month)
            ->get();

        if ($request->format === 'pdf') {
            return Pdf::loadView('reports.payroll-pdf', compact('payrolls', 'month'))
                ->stream("payroll-report-{$month}.pdf");
        }

        if ($request->format === 'excel') {
            return Excel::download(new PayrollExport($month), "payroll-{$month}.xlsx");
        }

        return view('reports.payroll', compact('payrolls', 'month'));
    }

    public function attendanceReport(Request $request): View
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);
        $start = \Carbon\Carbon::create((int)$year, (int)$mon)->startOfMonth()->toDateString();
        $end   = \Carbon\Carbon::create((int)$year, (int)$mon)->endOfMonth()->toDateString();

        $records = Attendance::with('employee.user')
            ->whereBetween('date', [$start, $end])
            ->get();

        return view('reports.attendance', compact('records', 'month'));
    }
}
