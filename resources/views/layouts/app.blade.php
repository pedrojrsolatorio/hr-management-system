<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'HRMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col">
        <div class="px-6 py-5 border-b border-gray-100">
            <span class="text-lg font-semibold text-indigo-600">HRMS</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Dashboard
            </a>

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
            <a href="{{ route('employees.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Employees
            </a>
            <a href="{{ route('departments.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('departments.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Departments
            </a>
            <a href="{{ route('positions.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('positions.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Positions
            </a>
            <a href="{{ route('attendance.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('attendance.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Attendance
            </a>
            <a href="{{ route('leaves.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('leaves.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Leave Requests
            </a>
            <a href="{{ route('performance-reviews.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('performance-reviews.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Performance
            </a>
            <a href="{{ route('reports.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('reports.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Reports
            </a>
            @endif

            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('payroll.index') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('payroll.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                Payroll
            </a>
            @endif

            @if(auth()->user()->hasRole('employee'))
            <a href="{{ route('employee.profile') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('employee.profile') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                My Profile
            </a>
            <a href="{{ route('attendance.my') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('attendance.my') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                My Attendance
            </a>
            <a href="{{ route('leaves.my') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('leaves.my') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                My Leaves
            </a>
            <a href="{{ route('payroll.my') }}"
               class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('payroll.my') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                My Payslips
            </a>
            @endif
        </nav>

        {{-- User info + logout --}}
        <div class="px-4 py-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <button type="submit" class="text-xs text-red-500 hover:underline">Sign out</button>
            </form>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="flex-1 flex flex-col">
        <header class="bg-white border-b border-gray-100 px-8 py-4">
            {{ $header ?? '' }}
        </header>
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>