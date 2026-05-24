<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Departments</h2>
            <a href="{{ route('departments.create') }}"
               class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                + New Department
            </a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">

        {{-- Flash messages --}}
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

        {{-- Search --}}
        <form method="GET" class="mb-6 flex gap-3">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search departments..."
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-indigo-300"
            />
            <button type="submit"
                class="px-4 py-2 bg-gray-100 border border-gray-200 text-sm rounded-lg hover:bg-gray-200">
                Search
            </button>
        </form>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Manager</th>
                        <th class="px-6 py-3 text-left">Employees</th>
                        <th class="px-6 py-3 text-left">Description</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($departments as $department)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                <a href="{{ route('departments.show', $department) }}"
                                   class="hover:text-indigo-600">
                                    {{ $department->name }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $department->manager?->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
                                    {{ $department->employees_count }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                                {{ $department->description ?? '—' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('departments.edit', $department) }}"
                                       class="text-indigo-600 hover:underline text-xs">Edit</a>
                                    <form method="POST"
                                          action="{{ route('departments.destroy', $department) }}"
                                          onsubmit="return confirm('Delete this department?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:underline text-xs">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No departments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $departments->links() }}
        </div>
    </div>
</x-app-layout>