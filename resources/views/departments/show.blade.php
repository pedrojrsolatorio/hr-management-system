<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ $department->name }}</h2>
            <a href="{{ route('departments.edit', $department) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto space-y-6">

        {{-- Info card --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-xs text-gray-500 mb-1">Manager</p>
                <p class="text-sm font-medium">{{ $department->manager?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Total employees</p>
                <p class="text-sm font-medium">{{ $department->employees_count }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Description</p>
                <p class="text-sm">{{ $department->description ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Created</p>
                <p class="text-sm">{{ $department->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Employees list --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-sm font-medium text-gray-700">Employees in this department</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Position</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($department->employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('employees.show', $employee) }}"
                                   class="font-medium text-gray-800 hover:text-indigo-600">
                                    {{ $employee->user->name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->employee_code }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $employee->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ ucfirst($employee->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">
                                No employees in this department.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>