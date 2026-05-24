<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Employees</h2>
            <div class="flex gap-2">
                <a href="{{ route('employees.index', ['status' => 'terminated', 'trashed' => '1']) }}"
                   class="px-4 py-2 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50">
                    View Terminated
                </a>
                <a href="{{ route('employees.create') }}"
                   class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                    + Add Employee
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 px-6 max-w-7xl mx-auto">

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

        {{-- Filters --}}
        <form method="GET" class="mb-6 flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by name..."
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 flex-1 min-w-48" />
            <select name="department"
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All departments</option>
                @foreach($departments as $id => $name)
                    <option value="{{ $id }}" {{ request('department') == $id ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
            <select name="status"
                class="border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                <option value="">All statuses</option>
                <option value="active"     {{ request('status') === 'active'     ? 'selected' : '' }}>Active</option>
                <option value="inactive"   {{ request('status') === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
            {{-- Keep trashed filter if currently viewing trashed --}}
            @if(request('trashed'))
                <input type="hidden" name="trashed" value="1" />
            @endif
            <button type="submit"
                class="px-4 py-2 bg-gray-100 border border-gray-200 text-sm rounded-lg hover:bg-gray-200">
                Filter
            </button>
            @if(request('trashed'))
                <a href="{{ route('employees.index') }}"
                   class="px-4 py-2 bg-indigo-50 text-indigo-700 text-sm rounded-lg hover:bg-indigo-100">
                    Back to Active
                </a>
            @endif
        </form>

        {{-- Trashed banner --}}
        @if(request('trashed'))
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 text-amber-700 rounded-lg text-sm">
                Showing soft-deleted (terminated) employees. You can restore or permanently delete them below.
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
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
                        <tr class="hover:bg-gray-50 {{ $employee->trashed() ? 'opacity-60' : '' }}">
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">
                                    @if($employee->profile_photo)
                                        <img src="{{ asset('storage/' . $employee->profile_photo) }}"
                                             class="w-8 h-8 rounded-full object-cover" />
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-xs font-medium">
                                            {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-800">{{ $employee->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-gray-500">{{ $employee->employee_code }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->department?->name ?? '—' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $employee->position?->title ?? '—' }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ match($employee->status) {
                                        'active'     => 'bg-green-50 text-green-700',
                                        'inactive'   => 'bg-gray-100 text-gray-500',
                                        'terminated' => 'bg-red-50 text-red-600',
                                        default      => 'bg-gray-100 text-gray-500',
                                    } }}">
                                    {{ ucfirst($employee->status) }}
                                    @if($employee->trashed())
                                        <span class="ml-1">(deleted)</span>
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex items-center gap-3">

                                    @if($employee->trashed())
                                        {{-- Restore button --}}
                                        <form method="POST"
                                              action="{{ route('employees.restore', $employee->id) }}">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="text-green-600 hover:underline text-xs">
                                                Restore
                                            </button>
                                        </form>

                                        {{-- Force delete button --}}
                                        <button type="button"
                                            onclick="openForceDeleteModal({{ $employee->id }}, '{{ addslashes($employee->user->name) }}')"
                                            class="text-red-600 hover:underline text-xs font-medium">
                                            Delete Permanently
                                        </button>

                                    @else
                                        {{-- Normal edit / soft delete --}}
                                        <a href="{{ route('employees.edit', $employee) }}"
                                           class="text-indigo-600 hover:underline text-xs">Edit</a>
                                        <form method="POST"
                                              action="{{ route('employees.destroy', $employee) }}"
                                              onsubmit="return confirm('Terminate this employee?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="text-red-500 hover:underline text-xs">
                                                Terminate
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                {{ request('trashed') ? 'No terminated employees found.' : 'No employees found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $employees->links() }}</div>
    </div>

    {{-- Force Delete Confirmation Modal --}}
    <div id="force-delete-modal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center"
         style="background: rgba(0,0,0,0.45);">
        <div class="bg-white rounded-xl border border-gray-100 p-6 w-full max-w-md mx-4">
            <h3 class="text-base font-semibold text-gray-800 mb-2">Permanently delete employee?</h3>
            <p class="text-sm text-gray-500 mb-1">
                You are about to permanently delete
                <span id="modal-employee-name" class="font-medium text-gray-800"></span>.
            </p>
            <p class="text-sm text-red-600 mb-6">
                This cannot be undone. Their login credentials will be erased.
                Payroll, attendance, and leave records will be kept but anonymised.
            </p>
            <div class="flex gap-3 justify-end">
                <button type="button"
                    onclick="closeForceDeleteModal()"
                    class="px-4 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <form id="force-delete-form" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        Yes, delete permanently
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openForceDeleteModal(id, name) {
            document.getElementById('modal-employee-name').textContent = name;
            document.getElementById('force-delete-form').action =
                '/employees/' + id + '/force';
            document.getElementById('force-delete-modal').classList.remove('hidden');
        }

        function closeForceDeleteModal() {
            document.getElementById('force-delete-modal').classList.add('hidden');
        }

        // Close modal on backdrop click
        document.getElementById('force-delete-modal').addEventListener('click', function(e) {
            if (e.target === this) closeForceDeleteModal();
        });
    </script>
    @endpush
</x-app-layout>