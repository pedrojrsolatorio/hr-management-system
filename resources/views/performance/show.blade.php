<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Performance Review</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 p-6 space-y-4 text-sm">
            <div class="grid grid-cols-2 gap-4">
                <div><p class="text-xs text-gray-500">Employee</p><p class="font-medium">{{ $performanceReview->employee->user->name }}</p></div>
                <div><p class="text-xs text-gray-500">Period</p><p>{{ $performanceReview->period }}</p></div>
                <div><p class="text-xs text-gray-500">Score</p>
                    <span class="px-2 py-1 rounded-full text-xs font-medium
                        {{ $performanceReview->score >= 80 ? 'bg-green-50 text-green-700' : 'bg-amber-50 text-amber-700' }}">
                        {{ $performanceReview->score }}/100
                    </span>
                </div>
                <div><p class="text-xs text-gray-500">Reviewer</p><p>{{ $performanceReview->reviewer->name }}</p></div>
            </div>
            <hr class="border-gray-100" />
            <div><p class="text-xs text-gray-500 mb-1">Strengths</p><p>{{ $performanceReview->strengths ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 mb-1">Areas for improvement</p><p>{{ $performanceReview->improvements ?? '—' }}</p></div>
            <div><p class="text-xs text-gray-500 mb-1">Comments</p><p>{{ $performanceReview->comments ?? '—' }}</p></div>
        </div>
    </div>
</x-app-layout>