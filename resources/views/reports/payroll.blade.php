<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Payroll Report — {{ $month }}</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-7xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-right">Gross</th>
                        <th class="px-6 py-3 text-right">Deductions</th>
                        <th class="px-6 py-3 text-right">Net</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payrolls as $payroll)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $payroll->employee->user->name }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="px-6 py-3 text-right text-red-600">{{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="px-6 py-3 text-right font-semibold">{{ number_format($payroll->net_salary, 2) }}</td>
                            <td class="px-6 py-3">{{ ucfirst($payroll->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">No payroll for this month.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>