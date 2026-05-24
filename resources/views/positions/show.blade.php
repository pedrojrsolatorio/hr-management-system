<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ $position->title }}</h2>
            <a href="{{ route('positions.edit', $position) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto space-y-6">

        <div class="bg-white rounded-xl border border-gray-100 p-6 flex gap-8">
            <div>
                <p class="text-xs text-gray-500 mb-1">Title</p>
                <p class="text-sm font-medium">{{ $position->title }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Level</p>
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700">
                    {{ ucfirst($position->level) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500 mb-1">Total employees</p>
                <p class="text-sm font-medium">{{ $position->employees_count }}</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-sm font-medium text-gray-700">Employees in this position</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Department</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($position->employees as $employee)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('employees.show', $employee) }}"
                                   class="font-medium text-gray-800 hover:text-indigo-600">
                                    {{ $employee->user->name }}
                                </a>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->employee_code }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
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
                                No employees in this position.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>