<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Payroll</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-7xl mx-auto space-y-6">

        {{-- Generate form --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5">
            <form method="POST" action="{{ route('payroll.generate') }}" class="flex gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Month</label>
                    <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                        class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    Generate All
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Month</th>
                        <th class="px-6 py-3 text-right">Basic</th>
                        <th class="px-6 py-3 text-right">Gross</th>
                        <th class="px-6 py-3 text-right">Deductions</th>
                        <th class="px-6 py-3 text-right">Net</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payrolls as $payroll)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $payroll->employee->user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $payroll->month }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">{{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">{{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="px-6 py-3 text-right text-red-600">{{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-800">{{ number_format($payroll->net_salary, 2) }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ match($payroll->status) {
                                        'paid'     => 'bg-green-50 text-green-700',
                                        'approved' => 'bg-blue-50 text-blue-700',
                                        default    => 'bg-gray-100 text-gray-500',
                                    } }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('payroll.show', $payroll) }}"
                                       class="text-indigo-600 hover:underline text-xs">View</a>
                                    <a href="{{ route('payroll.pdf', $payroll) }}"
                                       class="text-gray-500 hover:underline text-xs" target="_blank">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400 text-sm">No payroll records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payrolls->links() }}</div>
    </div>
</x-app-layout>