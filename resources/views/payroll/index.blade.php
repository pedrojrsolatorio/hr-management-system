<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Payroll</h2>
    </x-slot>
    <div class="mx-auto max-w-7xl space-y-6 px-6 py-8">

        {{-- Generate form --}}
        <div class="rounded-xl border border-gray-100 bg-white p-5">
            <form method="POST" action="{{ route('payroll.generate') }}" class="flex items-end gap-3">
                @csrf
                <div>
                    <label class="mb-1 block text-xs text-gray-500">Month</label>
                    <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                    Generate All
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}</div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
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
                            <td class="px-6 py-3 font-medium">
                                @if ($payroll->employee?->user)
                                    {{ $payroll->employee->user->name }}
                                @else
                                    <span class="italic text-gray-400">Deleted User</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $payroll->month }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">
                                {{ number_format($payroll->basic_salary, 2) }}</td>
                            <td class="px-6 py-3 text-right text-gray-600">
                                {{ number_format($payroll->gross_salary, 2) }}</td>
                            <td class="px-6 py-3 text-right text-red-600">
                                {{ number_format($payroll->total_deductions, 2) }}</td>
                            <td class="px-6 py-3 text-right font-semibold text-gray-800">
                                {{ number_format($payroll->net_salary, 2) }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ match ($payroll->status) {
                                        'paid' => 'bg-green-50 text-green-700',
                                        'approved' => 'bg-blue-50 text-blue-700',
                                        default => 'bg-gray-100 text-gray-500',
                                    } }} rounded-full px-2 py-1 text-xs font-medium">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('payroll.show', $payroll) }}"
                                        class="text-xs text-indigo-600 hover:underline">View</a>
                                    <a href="{{ route('payroll.pdf', $payroll) }}"
                                        class="text-xs text-gray-500 hover:underline" target="_blank">PDF</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sm text-gray-400">No payroll records.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $payrolls->links() }}</div>
    </div>
</x-app-layout>
