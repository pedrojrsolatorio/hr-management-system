<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">New Performance Review</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <form method="POST" action="{{ route('performance-reviews.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">— Select —</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Period (e.g. Q1-2024)</label>
                        <input type="text" name="period" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Score (1–100)</label>
                        <input type="number" name="score" min="1" max="100" required
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Strengths</label>
                    <textarea name="strengths" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Areas for improvement</label>
                    <textarea name="improvements" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Comments</label>
                    <textarea name="comments" rows="2"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Submit Review
                    </button>
                    <a href="{{ route('performance-reviews.index') }}"
                        class="px-5 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>