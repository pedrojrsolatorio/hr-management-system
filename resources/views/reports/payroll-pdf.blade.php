<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Payroll Report — {{ $month }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 32px; }
        .header { margin-bottom: 24px; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; display: flex; justify-content: space-between; align-items: flex-start; }
        .header h1 { font-size: 20px; font-weight: 700; color: #4f46e5; }
        .header p  { font-size: 11px; color: #6b7280; margin-top: 4px; }
        .summary { display: flex; gap: 16px; margin-bottom: 20px; }
        .summary-box { flex: 1; background: #f5f5ff; border: 1px solid #e0e7ff; border-radius: 6px; padding: 10px 14px; }
        .summary-box .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        .summary-box .value { font-size: 16px; font-weight: 700; color: #4f46e5; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #4f46e5; color: #fff; }
        thead th { padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
        thead th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px 10px; border-bottom: 0.5px solid #e5e7eb; font-size: 11px; }
        tbody td.right { text-align: right; }
        tbody td.red { color: #dc2626; text-align: right; }
        tbody td.bold { font-weight: 700; text-align: right; }
        tfoot tr { background: #1e1b4b; color: #fff; }
        tfoot td { padding: 10px; font-weight: 700; font-size: 11px; text-align: right; }
        tfoot td:first-child { text-align: left; }
        .badge { display: inline-block; padding: 2px 7px; border-radius: 20px; font-size: 10px; font-weight: 600; }
        .badge-paid     { background: #d1fae5; color: #065f46; }
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-draft    { background: #f3f4f6; color: #6b7280; }
        .footer { margin-top: 24px; font-size: 10px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>Payroll Report</h1>
            <p>Period: {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</p>
            <p>Generated: {{ now()->format('F j, Y \a\t h:i A') }}</p>
        </div>
    </div>

    {{-- Summary boxes --}}
    <div class="summary">
        <div class="summary-box">
            <div class="label">Total employees</div>
            <div class="value">{{ $payrolls->count() }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total gross</div>
            <div class="value">{{ number_format($payrolls->sum('gross_salary'), 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total deductions</div>
            <div class="value">{{ number_format($payrolls->sum('total_deductions'), 2) }}</div>
        </div>
        <div class="summary-box">
            <div class="label">Total net payout</div>
            <div class="value">{{ number_format($payrolls->sum('net_salary'), 2) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Employee</th>
                <th>Department</th>
                <th class="right">Basic</th>
                <th class="right">Gross</th>
                <th class="right">Deductions</th>
                <th class="right">Net</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrolls as $payroll)
                <tr>
                    <td>{{ $payroll->employee?->employee_code ?? '—' }}</td>
                    <td>{{ $payroll->employee?->user?->name ?? '—' }}</td>
                    <td>{{ $payroll->employee?->department?->name ?? '—' }}</td>
                    <td class="right">{{ number_format($payroll->basic_salary, 2) }}</td>
                    <td class="right">{{ number_format($payroll->gross_salary, 2) }}</td>
                    <td class="red">{{ number_format($payroll->total_deductions, 2) }}</td>
                    <td class="bold">{{ number_format($payroll->net_salary, 2) }}</td>
                    <td>
                        <span class="badge badge-{{ $payroll->status }}">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total</td>
                <td>{{ number_format($payrolls->sum('gross_salary'), 2) }}</td>
                <td>{{ number_format($payrolls->sum('total_deductions'), 2) }}</td>
                <td>{{ number_format($payrolls->sum('net_salary'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">HR Management System &nbsp;·&nbsp; Confidential</div>
</body>
</html>