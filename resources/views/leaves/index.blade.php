<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Leave Requests</h2>
    </x-slot>

    <div class="py-8 px-6 max-w-7xl mx-auto">

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Type</th>
                        <th class="px-6 py-3 text-left">Period</th>
                        <th class="px-6 py-3 text-left">Days</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($leaves as $leave)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $leave->employee->user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $leave->leaveType->name }}</td>
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
                            <td class="px-6 py-3">
                                @if($leave->status === 'pending')
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('leaves.approve', $leave) }}">
                                            @csrf @method('PATCH')
                                            <button class="text-green-600 hover:underline text-xs">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('leaves.reject', $leave) }}">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="rejection_reason" value="Not approved." />
                                            <button class="text-red-500 hover:underline text-xs">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No leave requests.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $leaves->links() }}</div>
    </div>
</x-app-layout>