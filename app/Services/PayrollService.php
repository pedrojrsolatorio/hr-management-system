<?php

namespace App\Services;

use App\Models\{Employee, Payroll, Attendance};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollService
{
    public function generate(Employee $employee, string $month): Payroll
    {
        if (Payroll::where('employee_id', $employee->id)->where('month', $month)->exists()) {
            throw new \Exception("Payroll already generated for {$employee->user->name} in {$month}.");
        }

        return DB::transaction(function () use ($employee, $month) {
            $basic       = (float) $employee->basic_salary;
            $workingDays = $this->workingDaysInMonth($month);

            // Use whereBetween instead of whereRaw to avoid argument count issues
            $start = Carbon::parse("{$month}-01")->startOfMonth();
            $end   = Carbon::parse("{$month}-01")->endOfMonth();

            $presentDays = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
                ->whereIn('status', ['present', 'late'])
                ->count();

            $absenceDeduction = $workingDays > 0
                ? ($basic / $workingDays) * max(0, $workingDays - $presentDays)
                : 0;

            $allowances = [
                ['label' => 'Housing Allowance',   'amount' => round($basic * 0.20, 2)],
                ['label' => 'Transport Allowance', 'amount' => round($basic * 0.10, 2)],
            ];

            $deductions = [
                ['label' => 'Income Tax (10%)',     'amount' => round($basic * 0.10, 2)],
                ['label' => 'Social Security (5%)', 'amount' => round($basic * 0.05, 2)],
                ['label' => 'Absence Deduction',    'amount' => round($absenceDeduction, 2)],
            ];

            $totalAllowances = collect($allowances)->sum('amount');
            $totalDeductions = collect($deductions)->sum('amount');
            $gross           = $basic + $totalAllowances;
            $net             = max(0, $gross - $totalDeductions);

            $payroll = Payroll::create([
                'employee_id'      => $employee->id,
                'month'            => $month,
                'basic_salary'     => $basic,
                'gross_salary'     => round($gross, 2),
                'total_deductions' => round($totalDeductions, 2),
                'net_salary'       => round($net, 2),
                'status'           => 'draft',
            ]);

            foreach ($allowances as $a) {
                $payroll->items()->create([...$a, 'type' => 'allowance']);
            }
            foreach ($deductions as $d) {
                $payroll->items()->create([...$d, 'type' => 'deduction']);
            }

            return $payroll;
        });
    }

    public function generateForAll(string $month): int
    {
        $count = 0;
        Employee::where('status', 'active')->each(function ($employee) use ($month, &$count) {
            try {
                $this->generate($employee, $month);
                $count++;
            } catch (\Exception) {
                // Already generated — skip
            }
        });
        return $count;
    }

    private function workingDaysInMonth(string $month): int
    {
        $start = Carbon::parse("{$month}-01");
        $end   = $start->copy()->endOfMonth();
        $days  = 0;
        while ($start->lte($end)) {
            if ($start->isWeekday()) $days++;
            $start->addDay();
        }
        return $days;
    }
}

// Note: using float for salary calculations is generally not recommended due to precision issues. In production, consider using a library like Brick\Money or storing amounts as integers (cents) to avoid rounding errors.