<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'HRMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {display: none !important}
    </style>
</head>
<body class="bg-gray-50 text-gray-900 font-sans">

<div
    x-data="{ open: localStorage.getItem('sidebar') !== 'hidden' }"
    x-init="$watch('open', value => localStorage.setItem('sidebar', value ? 'shown' : 'hidden'))"
    class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside 
        :class="open ? 'w-64' : 'w-20'" 
        class="bg-white border-r border-gray-100 flex flex-col transition-all duration-300 ease-in-out">
        
        <div class="px-4 py-5 border-b border-gray-100 flex items-center justify-between">
            <span class="text-lg font-bold text-indigo-600" x-show="open" x-cloak>
                HRMS
            </span>

            <button 
                @click="open = !open"
                class="text-gray-500 hover:text-indigo-600 transition"
                title="Toggle Sidebar"
            >
                ☰
            </button>
        </div>

        <nav class="flex-1 px-2 py-6 space-y-1 text-sm">

            @php
                $linkClass = "flex items-center gap-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out hover:translate-x-0.5 hover:bg-indigo-50";

                $activeClass = "text-indigo-700 font-medium";
                $inactiveClass = "text-gray-600";

                $isActive = fn($pattern) => request()->routeIs($pattern);
            @endphp

            <a href="{{ route('dashboard') }}"
               class="{{ $linkClass }} {{ $isActive('dashboard') ? $activeClass : $inactiveClass }}">
                <span>🏠</span>
                <span x-show="open" x-cloak>Dashboard</span>
            </a>

            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
                {{-- <a href="{{ route('employees.index') }}"
                class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                    Employees
                </a> --}}
                 <a href="{{ route('employees.index') }}" class="{{ $linkClass }} {{ $isActive('employees.index') ? $activeClass : $inactiveClass }}">
                    <span>👥</span><span x-show="open" x-cloak>Employees</span>
                </a>
                <a href="{{ route('departments.index') }}"
                class="{{ $linkClass }} {{ $isActive('departments.*') ? $activeClass : $inactiveClass }}">
                    <span>🏢</span><span x-show="open" x-cloak>Departments</span>
                </a>
                <a href="{{ route('positions.index') }}"
                class="{{ $linkClass }} {{ $isActive('positions.*') ? $activeClass : $inactiveClass }}">
                    <span>📌</span><span x-show="open" x-cloak>Positions</span>
                </a>
                <a href="{{ route('attendance.index') }}"
                class="{{ $linkClass }} {{ $isActive('attendance.index') ? $activeClass : $inactiveClass }}">
                    <span>⏱️</span><span x-show="open" x-cloak>Attendance</span>
                </a>
                <a href="{{ route('leaves.index') }}"
                class="{{ $linkClass }} {{ $isActive('leaves.index') ? $activeClass : $inactiveClass }}">
                    <span>🏖️</span><span x-show="open" x-cloak>Leave Requests</span>
                </a>
                <a href="{{ route('performance-reviews.index') }}"
                class="{{ $linkClass }} {{ $isActive('performance-reviews.*') ? $activeClass : $inactiveClass }}">
                    <span>📊</span><span x-show="open" x-cloak>Performance</span>
                </a>
                <a href="{{ route('reports.index') }}"
                class="{{ $linkClass }} {{ $isActive('reports.*') ? $activeClass : $inactiveClass }}">
                    <span>📁</span><span x-show="open" x-cloak>Reports</span>
                </a>
            @endif

            @if(auth()->user()->hasRole('admin'))
            <a href="{{ route('payroll.index') }}"
               class="{{ $linkClass }} {{ $isActive('payroll.*') ? $activeClass : $inactiveClass }}">
                <span>💰</span><span x-show="open" x-cloak>Payroll</span>
            </a>
            @endif

            @if(auth()->user()->hasRole('employee'))
            <a href="{{ route('employee.profile') }}"
               class="{{ $linkClass }} {{ $isActive('employee.profile') ? $activeClass : $inactiveClass }}">
                <span>🙍</span><span x-show="open" x-cloak>My Profile</span>
            </a>
            <a href="{{ route('attendance.my') }}"
               class="{{ $linkClass }} {{ $isActive('attendance.my') ? $activeClass : $inactiveClass }}">
                <span>⏱️</span><span x-show="open" x-cloak>My Attendance</span>
            </a>
            <a href="{{ route('leaves.my') }}"
               class="{{ $linkClass }} {{ $isActive('leaves.my') ? $activeClass : $inactiveClass }}">
                <span>🏖️</span><span x-show="open" x-cloak>My Leaves</span>
            </a>
            <a href="{{ route('payroll.my') }}"
               class="{{ $linkClass }} {{ $isActive('payroll.my') ? $activeClass : $inactiveClass }}">
                <span>💵</span><span x-show="open" x-cloak>My Payslips</span>
            </a>
            @endif
        </nav>

        {{-- User info + logout --}}
        <div class="px-4 py-4 border-t border-gray-100">
            <p class="text-xs text-gray-500 truncate" x-show="open" x-cloak>{{ auth()->user()->name }}</p>
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