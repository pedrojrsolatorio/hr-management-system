<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Positions</h2>
            <a href="{{ route('positions.create') }}"
                class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                + New Position
            </a>
        </div>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8">

        @if (session('success'))
            <div class="mb-4 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" class="mb-6 flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search positions..."
                class="flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            <select name="level"
                class="rounded-lg border border-gray-200 px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All levels</option>
                <option value="junior" {{ request('level') === 'junior' ? 'selected' : '' }}>Junior</option>
                <option value="mid" {{ request('level') === 'mid' ? 'selected' : '' }}>Mid-level</option>
                <option value="senior" {{ request('level') === 'senior' ? 'selected' : '' }}>Senior</option>
                <option value="lead" {{ request('level') === 'lead' ? 'selected' : '' }}>Lead</option>
                <option value="manager" {{ request('level') === 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="executive" {{ request('level') === 'executive' ? 'selected' : '' }}>Executive</option>
            </select>
            <button type="submit"
                class="rounded-lg border border-gray-200 bg-gray-100 px-4 py-2 text-sm hover:bg-gray-200">
                Filter
            </button>
        </form>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Level</th>
                        <th class="px-6 py-3 text-left">Employees</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($positions as $position)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                <a href="{{ route('positions.show', $position) }}" class="hover:text-indigo-600">
                                    {{ $position->title }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="{{ match ($position->level) {
                                        'junior' => 'bg-blue-50 text-blue-700',
                                        'mid' => 'bg-teal-50 text-teal-700',
                                        'senior' => 'bg-purple-50 text-purple-700',
                                        'lead' => 'bg-amber-50 text-amber-700',
                                        'manager' => 'bg-orange-50 text-orange-700',
                                        'executive' => 'bg-red-50 text-red-700',
                                        default => 'bg-gray-100 text-gray-600',
                                    } }} rounded-full px-2 py-1 text-xs font-medium">
                                    {{ ucfirst($position->level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $position->employees_count }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('positions.edit', $position) }}"
                                        class="text-xs text-indigo-600 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('positions.destroy', $position) }}"
                                        onsubmit="return confirm('Delete this position?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-400">
                                No positions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $positions->links() }}</div>
    </div>
</x-app-layout>
