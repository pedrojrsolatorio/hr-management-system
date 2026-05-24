<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">New Position</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <form method="POST" action="{{ route('positions.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Position title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('title') border-red-400 @enderror"
                        required />
                    @error('title')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select name="level"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('level') border-red-400 @enderror"
                        required>
                        <option value="">— Select level —</option>
                        @foreach($levels as $value => $label)
                            <option value="{{ $value }}" {{ old('level') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('level')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Create Position
                    </button>
                    <a href="{{ route('positions.index') }}"
                        class="px-5 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>