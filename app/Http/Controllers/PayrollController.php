<?php

namespace App\Http\Controllers;

use App\Models\{Payroll, Employee};
use App\Services\PayrollService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\{RedirectResponse, Request, Response};
use Illuminate\View\View;

class PayrollController extends Controller
{
    public function __construct(private PayrollService $service) {}

    public function index(Request $request): View
    {
        $payrolls = Payroll::with(['employee.user'])
            ->when(
                $request->filled('month'),
                fn($q) =>
                $q->where('month', $request->month)
            )
            ->latest()
            ->paginate(20);

        return view('payroll.index', compact('payrolls'));
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'month'       => 'required|date_format:Y-m',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        if ($request->filled('employee_id')) {
            $employee = Employee::findOrFail($request->employee_id);
            $this->service->generate($employee, $request->month);
            $count = 1;
        } else {
            $count = $this->service->generateForAll($request->month);
        }

        return redirect()->route('payroll.index')
            ->with('success', "Generated payroll for {$count} employee(s).");
    }

    public function show(Payroll $payroll): View
    {
        $payroll->load(['employee.user', 'employee.department', 'items']);

        return view('payroll.show', compact('payroll'));
    }

    public function pdf(Payroll $payroll): Response
    {
        $payroll->load(['employee.user', 'employee.department', 'employee.position', 'items']);

        $pdf = Pdf::loadView('payroll.pdf', compact('payroll'));

        return $pdf->stream(
            "payslip-{$payroll->employee->employee_code}-{$payroll->month}.pdf"
        );
    }

    public function myPayslips(): View
    {
        $user     = auth()->user();
        $payrolls = Payroll::where('employee_id', $user->employee->id)
            ->latest()
            ->paginate(12);

        return view('payroll.my', compact('payrolls'));
    }
}
