<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Edit — {{ $employee->user->name }}</h2>
    </x-slot>

    <div class="py-8 px-6 max-w-3xl mx-auto">
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <form method="POST" action="{{ route('employees.update', $employee) }}"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full name</label>
                        <input type="text" name="name"
                            value="{{ old('name', $employee->user->name) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select name="department_id"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">— None —</option>
                            @foreach($departments as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('department_id', $employee->department_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                        <select name="position_id"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">— None —</option>
                            @foreach($positions as $id => $title)
                                <option value="{{ $id }}"
                                    {{ old('position_id', $employee->position_id) == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hire date</label>
                        <input type="date" name="hire_date"
                            value="{{ old('hire_date', $employee->hire_date?->format('Y-m-d')) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Basic salary</label>
                        <input type="number" name="basic_salary"
                            value="{{ old('basic_salary', $employee->basic_salary) }}"
                            min="0" step="0.01"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="active"     {{ old('status', $employee->status) === 'active'     ? 'selected' : '' }}>Active</option>
                            <option value="inactive"   {{ old('status', $employee->status) === 'inactive'   ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone"
                            value="{{ old('phone', $employee->phone) }}"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select name="gender"
                            class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-300">
                            <option value="">— Select —</option>
                            <option value="male"   {{ old('gender', $employee->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $employee->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other"  {{ old('gender', $employee->gender) === 'other'  ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile photo</label>
                    @if($employee->profile_photo)
                        <img src="{{ asset('storage/' . $employee->profile_photo) }}"
                             class="w-16 h-16 rounded-full object-cover mb-2" />
                    @endif
                    <input type="file" name="profile_photo" accept="image/*"
                        class="text-sm text-gray-600" />
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                        Save Changes
                    </button>
                    <a href="{{ route('employees.show', $employee) }}"
                        class="px-5 py-2 border border-gray-200 text-sm rounded-lg hover:bg-gray-50">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>