<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">My Leave Requests</h2>
    </x-slot>

    <div class="py-8 px-6 max-w-5xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
        @endif

        {{-- Request form --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-4">New Leave Request</h3>
            <form method="POST" action="{{ route('leaves.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Leave type</label>
                    <select name="leave_type_id" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">— Select —</option>
                        @foreach($leaveTypes as $type)
                            <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->days_allowed }} days)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Reason</label>
                    <input type="text" name="reason" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        placeholder="Brief reason..." />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Start date</label>
                    <input type="date" name="start_date" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">End date</label>
                    <input type="date" name="end_date" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                </div>
                <div class="md:col-span-2">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>

        {{-- History --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Period</th>
                        <th class="px-6 py-3 text-left">Days</th>
                        <th class="px-6 py-3 text-left">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $leave)
                        <tr>
                            <td class="px-6 py-3">{{ $leave->leaveType->name }}</td>
                            <td class="px-6 py-3 text-gray-600">
                                {{ $leave->start_date->format('M d') }} – {{ $leave->end_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $leave->total_days }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ match($leave->status) {
                                        'approved' => 'bg-green-50 text-green-700',
                                        'rejected' => 'bg-red-50 text-red-600',
                                        default    => 'bg-amber-50 text-amber-700',
                                    } }}">
                                    {{ ucfirst($leave->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-400 text-sm">No leave requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $leaves->links() }}</div>
    </div>
</x-app-layout>