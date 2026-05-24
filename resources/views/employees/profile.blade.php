<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">My Profile</h2>
    </x-slot>

    <div class="py-8 px-6 max-w-4xl mx-auto space-y-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 flex gap-6 items-start">
            @if($employee->profile_photo)
                <img src="{{ asset('storage/' . $employee->profile_photo) }}"
                     class="w-20 h-20 rounded-full object-cover" />
            @else
                <div class="w-20 h-20 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 text-2xl font-semibold">
                    {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                </div>
            @endif
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6 flex-1">
                <div><p class="text-xs text-gray-500">Name</p><p class="text-sm font-medium">{{ $employee->user->name }}</p></div>
                <div><p class="text-xs text-gray-500">Email</p><p class="text-sm">{{ $employee->user->email }}</p></div>
                <div><p class="text-xs text-gray-500">Code</p><p class="text-sm">{{ $employee->employee_code }}</p></div>
                <div><p class="text-xs text-gray-500">Department</p><p class="text-sm">{{ $employee->department?->name ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500">Position</p><p class="text-sm">{{ $employee->position?->title ?? '—' }}</p></div>
                <div><p class="text-xs text-gray-500">Hire date</p><p class="text-sm">{{ $employee->hire_date->format('M d, Y') }}</p></div>
            </div>
        </div>
    </div>
</x-app-layout>