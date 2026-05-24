<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Attendance Records</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">

        {{-- Filters --}}
        <form method="GET" class="mb-6 flex flex-wrap gap-3">
            <select name="employee_id"
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All employees</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                        {{ $emp->user->name }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ request('date') }}"
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            <button type="submit"
                class="px-4 py-2 bg-gray-100 border border-gray-200 text-sm rounded-lg hover:bg-gray-200">
                Filter
            </button>
            <a href="{{ route('attendance.report') }}"
                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 ml-auto">
                Monthly Report
            </a>
        </form>

        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Check In</th>
                        <th class="px-6 py-3 text-left">Check Out</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">
                                {{ $record->employee->user->name }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $record->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $record->check_in ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $record->check_out ?? '—' }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ match($record->status) {
                                        'present' => 'bg-green-50 text-green-700',
                                        'late'    => 'bg-amber-50 text-amber-700',
                                        'absent'  => 'bg-red-50 text-red-700',
                                        default   => 'bg-gray-100 text-gray-500',
                                    } }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>
</x-app-layout>