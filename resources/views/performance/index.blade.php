<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Performance Reviews</h2>
            <a href="{{ route('performance-reviews.create') }}"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                + New Review
            </a>
        </div>
    </x-slot>
    <div class="mx-auto max-w-7xl px-6 py-8">
        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
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
                            <td class="px-6 py-3 font-medium">
                                <a
                                    href="{{ route('performance-reviews.show', $review) }}">{{ $review->employee->user->name }}</a>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->period }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ $review->score >= 80 ? 'bg-green-50 text-green-700' : ($review->score >= 60 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-600') }} rounded-full px-2 py-1 text-xs font-medium">
                                    {{ $review->score }}/100
                                </span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->reviewer->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $review->reviewed_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">No reviews yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $reviews->links() }}</div>
    </div>
</x-app-layout>
