<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Monthly Attendance Report</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8">

        {{-- Month picker --}}
        <form method="GET" class="mb-6 flex items-end gap-3">
            <div>
                <label class="mb-1 block text-xs text-gray-500">Month</label>
                <input type="month" name="month" value="{{ $month }}"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            </div>
            <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                Generate
            </button>
        </form>

        {{-- Summary table --}}
        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <div class="flex items-center justify-between border-b border-gray-50 px-6 py-4">
                <h3 class="text-sm font-medium text-gray-700">
                    Report for {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                </h3>
                <span class="text-xs text-gray-400">{{ $summary->count() }} employees</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-center">Present</th>
                        <th class="px-6 py-3 text-center">Late</th>
                        <th class="px-6 py-3 text-center">Half-day</th>
                        <th class="px-6 py-3 text-center">Absent</th>
                        <th class="px-6 py-3 text-center">Total Days</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($summary as $row)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $row['employee'] }}</td>
                            <td class="px-6 py-3 text-gray-500">{{ $row['code'] }}</td>
                            <td class="px-6 py-3 text-center">
                                <span class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700">
                                    {{ $row['present'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                                    {{ $row['late'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="rounded-full bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700">
                                    {{ $row['half_day'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700">
                                    {{ $row['absent'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                                No attendance data for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
