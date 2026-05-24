<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Attendance Report — {{ $month }}</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-7xl mx-auto">
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
                    @forelse($records as $record)
                        <tr>
                            <td class="px-6 py-3 font-medium">{{ $record->employee->user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $record->date->format('M d, Y') }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $record->check_in ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $record->check_out ?? '—' }}</td>
                            <td class="px-6 py-3">{{ ucfirst($record->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">No records.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>