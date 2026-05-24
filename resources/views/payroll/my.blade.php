<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">My Payslips</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-5xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Month</th>
                        <th class="px-6 py-3 text-right">Net Salary</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $payroll->month }}</td>
                            <td class="px-6 py-3 text-right font-semibold">{{ number_format($payroll->net_salary, 2) }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $payroll->status === 'paid' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <a href="{{ route('payroll.pdf', $payroll) }}" target="_blank"
                                   class="text-indigo-600 hover:underline text-xs">Download PDF</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">No payslips yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payrolls->links() }}</div>
    </div>
</x-app-layout>