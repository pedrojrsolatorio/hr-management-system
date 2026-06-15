<x-app-layout>
    @php
        $showingTrashed = request('trashed') || request('status') === 'terminated';
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Employees</h2>
            <div class="flex gap-2">
                {{-- Note: putting 'status' => 'terminated' here looks redundant --}}
                <a href="{{ route('employees.index', ['status' => 'terminated', 'trashed' => '1']) }}"
                    class="rounded-lg border border-gray-200 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                    View Terminated
                </a>
                <a href="{{ route('employees.create') }}"
                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                    + Add Employee
                </a>
            </div>
        </div>
    </x-slot>

    <div x-data="{ open: false, name: '', action: '' }" class="mx-auto max-w-7xl px-6 py-8">

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
        <form method="GET" class="mb-6 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..."
                class="min-w-48 flex-1 rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            <select name="department"
                class="rounded-lg border border-gray-200 px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All departments</option>
                @foreach ($departments as $id => $name)
                    <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <select name="status"
                class="rounded-lg border border-gray-200 px-4 py-2 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated
                </option>
            </select>
            {{-- Keep trashed filter if currently viewing trashed --}}
            {{-- but commented cause only needed to preserve trashed=1 across form submissions on previous code version that's not using $showingTrashed --}}
            {{-- @if (request('trashed'))
                <input type="hidden" name="trashed" value="1" />
            @endif --}}
            <button type="submit"
                class="rounded-lg border border-gray-200 bg-gray-100 px-4 py-2 text-sm hover:bg-gray-200">
                Filter
            </button>
            @if ($showingTrashed)
                <a href="{{ route('employees.index') }}"
                    class="rounded-lg bg-indigo-50 px-4 py-2 text-sm text-indigo-700 hover:bg-indigo-100">
                    Back to Active
                </a>
            @endif
        </form>

        {{-- Trashed banner --}}
        @if ($showingTrashed)
            <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-700">
                Showing soft-deleted (terminated) employees. You can restore or permanently delete them below.
            </div>
        @endif

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                    <tr>
                        <th class="px-6 py-3 text-left">Employee</th>
                        <th class="px-6 py-3 text-left">Code</th>
                        <th class="px-6 py-3 text-left">Department</th>
                        <th class="px-6 py-3 text-left">Position</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($employees as $employee)
                        <tr class="{{ $employee->trashed() ? 'opacity-60' : '' }} hover:bg-gray-50">
                            <td class="px-6 py-3">
                                <a href="{{ route('employees.show', $employee) }}" class="flex items-center gap-3">
                                    @if ($employee->profile_photo)
                                        <img src="{{ asset('storage/' . $employee->profile_photo) }}" width='32'
                                            height='32'
                                            class="h-8 w-8 flex-shrink-0 rounded-full object-cover object-center" />
                                    @else
                                        <div
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-700">
                                            {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $employee->user->name }}</span>
                                </a>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->employee_code }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="{{ match ($employee->status) {
                                        'active' => 'bg-green-50 text-green-700',
                                        'inactive' => 'bg-gray-100 text-gray-500',
                                        'terminated' => 'bg-red-50 text-red-600',
                                        default => 'bg-gray-100 text-gray-500',
                                    } }} rounded-full px-2 py-1 text-xs font-medium">
                                    {{ ucfirst($employee->status) }}
                                    @if ($employee->trashed())
                                        <span class="ml-1">(deleted)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">

                                    @if ($employee->trashed())
                                        {{-- Restore button --}}
                                        <form method="POST" action="{{ route('employees.restore', $employee->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-green-600 hover:underline">
                                                Restore
                                            </button>
                                        </form>

                                        {{-- Force delete button --}}
                                        <button type="button"
                                            @click="open = true; name = @js($employee->user->name); action = '{{ route('employees.force-destroy', $employee->id) }}';"
                                            class="text-xs font-medium text-red-600 hover:underline">
                                            Delete Permanently
                                        </button>
                                    @else
                                        {{-- Normal edit / soft delete --}}
                                        <a href="{{ route('employees.edit', $employee) }}"
                                            class="text-xs text-indigo-600 hover:underline">Edit</a>
                                        <form method="POST" action="{{ route('employees.destroy', $employee) }}"
                                            onsubmit="return confirm('Terminate this employee?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-xs text-red-500 hover:underline">
                                                Terminate
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-400">
                                {{ $showingTrashed ? 'No terminated employees found.' : 'No employees found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $employees->links() }}</div>

        {{-- Force Delete Confirmation Modal --}}
        <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center"
            style="background: rgba(0,0,0,0.45);">
            <div class="mx-4 w-full max-w-md rounded-xl border border-gray-100 bg-white p-6">
                <h3 class="mb-2 text-base font-semibold text-gray-800">Permanently delete employee?</h3>
                <p class="mb-1 text-sm text-gray-500">
                    You are about to permanently delete
                    <span x-text="name" class="font-medium text-gray-800"></span>.
                </p>
                <p class="mb-6 text-sm text-red-600">
                    This cannot be undone. Their login credentials will be erased.
                    Payroll, attendance, and leave records will be kept but anonymised.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="open = false"
                        class="rounded-lg border border-gray-200 px-4 py-2 text-sm hover:bg-gray-50">
                        Cancel
                    </button>
                    <form :action="action" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
                            Yes, delete permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
