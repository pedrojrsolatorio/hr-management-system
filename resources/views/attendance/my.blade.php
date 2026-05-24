<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">My Attendance</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto">

        {{-- Check in / out buttons --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 mb-6 flex items-center gap-4">
            <div class="flex-1">
                <p class="text-sm text-gray-500">Today</p>
                <p class="text-lg font-semibold text-gray-800">{{ now()->format('l, F j Y') }}</p>
                <p class="text-2xl font-mono mt-1" id="live-clock"></p>
            </div>
            <form method="POST" action="{{ route('attendance.checkin') }}">
                @csrf
                <button type="submit"
                    class="px-5 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                    Check In
                </button>
            </form>
            <form method="POST" action="{{ route('attendance.checkout') }}">
                @csrf
                <button type="submit"
                    class="px-5 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                    Check Out
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- History table --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
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
                    @forelse($attendances as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 text-gray-700">
                                {{ $record->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $record->check_in ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $record->check_out ?? '—' }}</td>
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
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No attendance records yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $attendances->links() }}</div>
    </div>

    @push('scripts')
    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('live-clock').textContent =
                now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @endpush
</x-app-layout>