<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\{FromCollection, WithHeadings, WithStyles, WithColumnWidths};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class EmployeesExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{
    public function collection(): Collection
    {
        return Employee::with(['user', 'department', 'position'])
            ->where('status', 'active')
            ->get()
            ->map(fn($e) => [
                $e->employee_code,
                $e->user->name,
                $e->user->email,
                $e->department?->name ?? '—',
                $e->position?->title  ?? '—',
                $e->hire_date->format('Y-m-d'),
                number_format($e->basic_salary, 2),
                ucfirst($e->status),
            ]);
    }

    public function headings(): array
    {
        return ['Code', 'Name', 'Email', 'Department', 'Position', 'Hire Date', 'Salary', 'Status'];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 24,
            'C' => 28,
            'D' => 18,
            'E' => 18,
            'F' => 14,
            'G' => 14,
            'H' => 12,
        ];
    }
}
