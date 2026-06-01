<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title>Payslip — {{ $payroll->employee?->employee_code }} — {{ $payroll->month }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; padding: 40px; }

        .company-header { text-align: center; margin-bottom: 28px; }
        .company-header h1 { font-size: 22px; font-weight: 700; color: #4f46e5; letter-spacing: -0.5px; }
        .company-header p { font-size: 11px; color: #6b7280; margin-top: 4px; }

        .payslip-title { text-align: center; margin-bottom: 24px; }
        .payslip-title h2 { font-size: 15px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; color: #374151; }
        .payslip-title p { font-size: 12px; color: #6b7280; margin-top: 4px; }

        .info-grid { display: flex; gap: 0; border: 1px solid #e5e7eb; border-radius: 6px; margin-bottom: 24px; overflow: hidden; }
        .info-block { flex: 1; padding: 14px 16px; border-right: 1px solid #e5e7eb; }
        .info-block:last-child { border-right: none; }
        .info-block .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; margin-bottom: 3px; }
        .info-block .val { font-size: 12px; font-weight: 600; color: #111827; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .section-heading { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; padding: 8px 0 4px; font-weight: 600; }
        thead th { background: #f9fafb; padding: 8px 10px; text-align: left; font-size: 10px; color: #6b7280; border-bottom: 1px solid #e5e7eb; }
        thead th.right { text-align: right; }
        tbody td { padding: 9px 10px; border-bottom: 0.5px solid #f3f4f6; font-size: 11px; }
        tbody td.right { text-align: right; }
        tbody td.green { color: #059669; font-weight: 600; text-align: right; }
        tbody td.red   { color: #dc2626; font-weight: 600; text-align: right; }

        .net-box { background: #4f46e5; color: #fff; border-radius: 8px; padding: 18px 20px; margin-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .net-box .net-label { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.85; }
        .net-box .net-amount { font-size: 22px; font-weight: 700; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; }
        .sig-block { text-align: center; width: 42%; }
        .sig-line { border-top: 1px solid #374151; margin-top: 36px; padding-top: 6px; font-size: 11px; color: #6b7280; }

        .footer { margin-top: 28px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 0.5px solid #e5e7eb; padding-top: 12px; }
    </style>
</head>
<body>

    <div class="company-header">
        <h1>HR Management System</h1>
        <p>Human Resource Department &nbsp;·&nbsp; Confidential</p>
    </div>

    <div class="payslip-title">
        <h2>Payslip</h2>
        <p>Pay period: {{ \Carbon\Carbon::parse($payroll->month.'-01')->format('F Y') }}</p>
    </div>

    <div class="info-grid">
        <div class="info-block">
            <div class="lbl">Employee name</div>
            <div class="val">{{ $payroll->employee?->user?->name ?? '—' }}</div>
        </div>
        <div class="info-block">
            <div class="lbl">Employee code</div>
            <div class="val">{{ $payroll->employee?->employee_code ?? '—' }}</div>
        </div>
        <div class="info-block">
            <div class="lbl">Department</div>
            <div class="val">{{ $payroll->employee?->department?->name ?? '—' }}</div>
        </div>
        <div class="info-block">
            <div class="lbl">Position</div>
            <div class="val">{{ $payroll->employee?->position?->title ?? '—' }}</div>
        </div>
        <div class="info-block">
            <div class="lbl">Status</div>
            <div class="val">{{ ucfirst($payroll->status) }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Earnings</th>
                <th class="right">Deductions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Basic salary row --}}
            <tr>
                <td>Basic Salary</td>
                <td class="green">{{ number_format($payroll->basic_salary, 2) }}</td>
                <td></td>
            </tr>

            {{-- Allowances --}}
            @foreach($payroll->items->where('type', 'allowance') as $item)
                <tr>
                    <td>{{ $item->label }}</td>
                    <td class="green">{{ number_format($item->amount, 2) }}</td>
                    <td></td>
                </tr>
            @endforeach

            {{-- Deductions --}}
            @foreach($payroll->items->where('type', 'deduction') as $item)
                <tr>
                    <td>{{ $item->label }}</td>
                    <td></td>
                    <td class="red">{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach

            {{-- Subtotals --}}
            <tr style="background:#f9fafb;font-weight:600">
                <td style="font-size:10px;text-transform:uppercase;letter-spacing:0.5px;color:#6b7280">Subtotals</td>
                <td class="right" style="font-weight:700">{{ number_format($payroll->gross_salary, 2) }}</td>
                <td class="right" style="color:#dc2626;font-weight:700">{{ number_format($payroll->total_deductions, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="net-box">
        <div class="net-label">Net salary</div>
        <div class="net-amount">{{ number_format($payroll->net_salary, 2) }}</div>
    </div>

    <div class="signatures">
        <div class="sig-block">
            <div class="sig-line">Prepared by — HR Department</div>
        </div>
        <div class="sig-block">
            <div class="sig-line">Received by — Employee Signature</div>
        </div>
    </div>

    <div class="footer">
        This is a system-generated payslip. &nbsp;·&nbsp;
        {{ \Carbon\Carbon::parse($payroll->month.'-01')->format('F Y') }} &nbsp;·&nbsp;
        HR Management System
    </div>

</body>
</html>