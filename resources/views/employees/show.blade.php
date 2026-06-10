<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ $employee->user->name }}</h2>
            <a href="{{ route('employees.edit', $employee) }}"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl space-y-6 px-6 py-8">

        {{-- Profile card --}}
        <div class="flex items-start gap-6 rounded-xl border border-gray-100 bg-white p-6">
            @if ($employee->profile_photo)
                <img src="{{ asset('storage/' . $employee->profile_photo) }}"
                    class="h-20 w-20 rounded-full object-cover" />
            @else
                <div
                    class="flex h-20 w-20 items-center justify-center rounded-full bg-indigo-100 text-2xl font-semibold text-indigo-700">
                    {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                </div>
            @endif
            <div class="grid flex-1 grid-cols-2 gap-6 md:grid-cols-4">
                <div>
                    <p class="text-xs text-gray-500">Code</p>
                    <p class="text-sm font-medium">{{ $employee->employee_code }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Email</p>
                    <p class="text-sm">{{ $employee->user->email }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Department</p>
                    <p class="text-sm">{{ $employee->department?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Position</p>
                    <p class="text-sm">{{ $employee->position?->title ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Hire date</p>
                    <p class="text-sm">{{ $employee->hire_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Salary</p>
                    <p class="text-sm">{{ number_format($employee->basic_salary, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Phone</p>
                    <p class="text-sm">{{ $employee->phone ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span
                        class="{{ $employee->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }} rounded-full px-2 py-1 text-xs font-medium">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Recent attendance --}}
        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <div class="border-b border-gray-50 px-6 py-4">
                <h3 class="text-sm font-medium text-gray-700">Recent Attendance</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Check In</th>
                        <th class="px-6 py-3 text-left">Check Out</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recentAttendance as $att)
                        <tr>
                            <td class="px-6 py-3">{{ $att->date->format('M d, Y') }}</td>
                            <td class="px-6 py-3">{{ $att->check_in ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $att->check_out ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ match ($att->status) {
                                        'present' => 'bg-green-50 text-green-700',
                                        'late' => 'bg-amber-50 text-amber-700',
                                        default => 'bg-red-50 text-red-600',
                                    } }} rounded-full px-2 py-1 text-xs">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">No records.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
