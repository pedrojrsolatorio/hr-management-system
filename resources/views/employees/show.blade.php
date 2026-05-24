<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">{{ $employee->user->name }}</h2>
            <a href="{{ route('employees.edit', $employee) }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Edit
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-6 max-w-5xl mx-auto space-y-6">

        {{-- Profile card --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 flex gap-6 items-start">
            @if($employee->profile_photo)
                <img src="{{ asset('storage/' . $employee->profile_photo) }}"
                     class="w-20 h-20 rounded-full object-cover" />
            @else
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-semibold">
                    {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                </div>
            @endif
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 flex-1">
                <div><p class="text-xs text-gray-500">Code</p><p class="text-sm font-medium">{{ $employee->employee_code }}</p></div>
                <div><p class="text-xs text-gray-500">Email</p><p class="text-sm">{{ $employee->user->email }}</p></div>
                <div><p class="text-xs text-gray-500">Department</p><p class="text-sm">{{ $employee->department?->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500">Position</p><p class="text-sm">{{ $employee->position?->title ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500">Hire date</p><p class="text-sm">{{ $employee->hire_date->format('M d, Y') }}</p></div>
                <div><p class="text-xs text-gray-500">Salary</p><p class="text-sm">{{ number_format($employee->basic_salary, 2) }}</p></div>
                <div><p class="text-xs text-gray-500">Phone</p><p class="text-sm">{{ $employee->phone ?? '—' }}</p></div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $employee->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($employee->status) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Recent attendance --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50">
                <h3 class="text-sm font-medium text-gray-700">Recent Attendance</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Check In</th>
                        <th class="px-6 py-3 text-left">Check Out</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employee->attendance->take(10) as $att)
                        <tr>
                            <td class="px-6 py-3">{{ $att->date->format('M d, Y') }}</td>
                            <td class="px-6 py-3">{{ $att->check_in ?? '—' }}</td>
                            <td class="px-6 py-3">{{ $att->check_out ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs
                                    {{ match($att->status) {
                                        'present' => 'bg-green-50 text-green-700',
                                        'late'    => 'bg-amber-50 text-amber-700',
                                        default   => 'bg-red-50 text-red-600',
                                    } }}">
                                    {{ ucfirst($att->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>