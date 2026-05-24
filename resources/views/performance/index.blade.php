<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Performance Reviews</h2>
            <a href="{{ route('performance-reviews.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                + New Review
            </a>
        </div>
    </x-slot>
    <div class="py-8 px-6 max-w-7xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Period</th>
                        <th class="px-6 py-3 text-left">Score</th>
                        <th class="px-6 py-3 text-left">Reviewer</th>
                        <th class="px-6 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium">{{ $review->employee->user->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->period }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $review->score >= 80 ? 'bg-green-50 text-green-700' : ($review->score >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }}">
                                    {{ $review->score }}/100
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->reviewer->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->reviewed_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">No reviews yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</x-app-layout>