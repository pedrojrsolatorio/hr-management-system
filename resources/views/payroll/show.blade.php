<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Payslip — {{ $payroll->month }}</h2>
            <a href="{{ route('payroll.pdf', $payroll) }}" target="_blank"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                Download PDF
            </a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-2xl space-y-6 px-6 py-8">
        <div class="space-y-4 rounded-xl border border-gray-100 bg-white p-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-500">Employee</p>
                    <p class="font-medium">
                        @if ($payroll->employee?->user)
                            {{ $payroll->employee->user->name }}
                        @else
                            <span class="italic text-gray-400">Deleted User</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Code</p>
                    <p>
                        @if ($payroll->employee?->employee_code)
                            {{ $payroll->employee->employee_code }}
                        @else
                            <span class="italic text-gray-400">N/A</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Department</p>
                    <p>{{ $payroll->employee->department?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Month</p>
                    <p>{{ $payroll->month }}</p>
                </div>
            </div>
            <hr class="border-gray-100" />
            <table class="w-full text-sm">
                <tbody>
                    @foreach ($payroll->items as $item)
                        <tr class="border-b border-gray-50">
                            <td class="py-2 text-gray-600">{{ $item->label }}</td>
                            <td
                                class="{{ $item->type === 'deduction' ? 'text-red-600' : 'text-green-700' }} py-2 text-right">
                                {{ $item->type === 'deduction' ? '-' : '+' }}{{ number_format($item->amount, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    <tr class="font-semibold">
                        <td class="py-3">Net Salary</td>
                        <td class="py-3 text-right text-lg text-indigo-700">
                            {{ number_format($payroll->net_salary, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
