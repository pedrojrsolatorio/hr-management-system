<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Employee Report</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-7xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Department</th>
                        <th class="px-6 py-3 text-left">Position</th>
                        <th class="px-6 py-3 text-left">Hire Date</th>
                        <th class="px-6 py-3 text-right">Salary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($employees as $employee)
                        <tr>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->employee_code }}</td>
                            <td class="px-6 py-3 font-medium">{{ $employee->user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->hire_date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-right">{{ number_format($employee->basic_salary, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>