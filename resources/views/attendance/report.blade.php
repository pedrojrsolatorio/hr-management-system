<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Monthly Attendance Report</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">

        {{-- Month picker --}}
        <form method="GET" class="mb-6 flex gap-3 items-end">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Month</label>
                <input type="month" name="month" value="{{ $month }}"
                    class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            </div>
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                Generate
            </button>
        </form>

        {{-- Summary table --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-700">
                    Report for {{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}
                </h3>
                <span class="text-xs text-gray-400">{{ $summary->count() }} employees</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-center">Present</th>
                        <th class="px-6 py-3 text-center">Late</th>
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
                                <span class="px-2 py-1 bg-green-50 text-green-700 rounded-full text-xs font-medium">
                                    {{ $row['present'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-1 bg-amber-50 text-amber-700 rounded-full text-xs font-medium">
                                    {{ $row['late'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2 py-1 bg-red-50 text-red-700 rounded-full text-xs font-medium">
                                    {{ $row['absent'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-center text-gray-600">{{ $row['total'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No attendance data for this month.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>