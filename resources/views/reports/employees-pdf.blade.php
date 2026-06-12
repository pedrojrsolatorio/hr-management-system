<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Employee Report</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* PDF page margin */
        @page {
            margin: 32px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            margin: 0;
            padding: 0;
        }

        /* inner safety spacing */
        .container {
            padding: 0 10px;
        }

        .header {
            margin-bottom: 24px;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 16px;
            padding-top: 16px;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #4f46e5;
        }

        .header p {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* margin-top: 16px; */
            /* table-layout: fixed; */
        }

        thead tr {
            background: #4f46e5;
            color: #fff;
        }

        thead th {
            padding: 8px 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            /* letter-spacing: 0.5px; */
            background: #4f46e5;
            color: #fff;
        }

        tbody tr:nth-child(even) {
            background: #f5f5ff;
        }

        tbody td {
            padding: 8px 10px;
            border-bottom: 0.5px solid #e5e7eb;
            font-size: 11px;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #f3f4f6;
            color: #6b7280;
        }

        .badge-terminated {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: #9ca3af;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Employee Report</h1>
            <p>Generated on {{ now()->format('F j, Y \a\t h:i A') }} &nbsp;·&nbsp; {{ $employees->count() }} employees
            </p>
        </div>

        <table>
            <colgroup>
                <col style="width: 60px;"> <!-- Code -->
                <col style="width: 120px;"> <!-- Name -->
                <col style="width: 180px;"> <!-- Email -->
                <col style="width: 120px;"> <!-- Department -->
                <col style="width: 120px;"> <!-- Position -->
                <col style="width: 90px;"> <!-- Hire Date -->
                <col style="width: 90px;"> <!-- Salary -->
                <col style="width: 90px;"> <!-- Status -->
            </colgroup>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Hire Date</th>
                    <th>Salary</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->user?->name ?? '—' }}</td>
                        <td>{{ $employee->user?->email ?? '—' }}</td>
                        <td>{{ $employee->department?->name ?? '—' }}</td>
                        <td>{{ $employee->position?->title ?? '—' }}</td>
                        <td>{{ $employee->hire_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ number_format($employee->basic_salary, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $employee->status }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">HR Management System &nbsp;·&nbsp; Confidential</div>
    </div>
</body>

</html>
