<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edit Department — {{ $department->name }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-2xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <form method="POST" action="{{ route('departments.update', $department) }}" class="space-y-5">
                @csrf @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Department name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                        value="{{ old('name', $department->name) }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 @error('name') border-red-400 @enderror"
                        required />
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manager</label>
                    <select name="manager_id"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                        <option value="">— No manager —</option>
                        @foreach($managers as $id => $name)
                            <option value="{{ $id }}"
                                {{ old('manager_id', $department->manager_id) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">{{ old('description', $department->description) }}</textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Save Changes
                    </button>
                    <a href="{{ route('departments.show', $department) }}"
                        class="px-5 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>