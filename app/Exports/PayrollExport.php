<?php

namespace App\Exports;

use App\Models\Payroll;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithStyles, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class PayrollExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function __construct(private string $month) {}

    public function collection(): Collection
    {
        return Payroll::with(['employee.user', 'employee.department'])
            ->where('month', $this->month)
            ->get()
            ->map(fn($p) => [
                $p->employee->employee_code,
                $p->employee->user->name,
                $p->employee->department?->name ?? '—',
                $p->month,
                number_format($p->basic_salary, 2),
                number_format($p->gross_salary, 2),
                number_format($p->total_deductions, 2),
                number_format($p->net_salary, 2),
                ucfirst($p->status),
            ]);
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Department', 'Month', 'Basic', 'Gross', 'Deductions', 'Net', 'Status'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }

    public function columnWidths(): array
    {
        return ['A' => 12, 'B' => 24, 'C' => 18, 'D' => 10, 'E' => 12, 'F' => 12, 'G' => 14, 'H' => 12, 'I' => 10];
    }
}
