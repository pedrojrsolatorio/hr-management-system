<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important
        }
    </style>
</head>

<body class="bg-gray-50 font-sans text-gray-900 antialiased">

    <div x-data="{ open: localStorage.getItem('sidebar') !== 'hidden' }" x-init="$watch('open', value => localStorage.setItem('sidebar', value ? 'shown' : 'hidden'))" class="flex h-screen">

        {{-- @include('layouts.navigation') --}}

        {{-- Sidebar --}}
        <aside :class="open ? 'w-64' : 'w-20'"
            class="flex flex-col overflow-y-auto border-r border-gray-100 bg-white transition-all duration-300 ease-in-out">

            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-5">
                <span class="text-lg font-bold text-indigo-600" x-show="open" x-cloak>
                    HRMS
                </span>

                <button @click="open = !open" class="text-gray-500 transition hover:text-indigo-600"
                    title="Toggle Sidebar">
                    ☰
                </button>
            </div>

            <nav class="flex-1 space-y-1 px-2 py-6 text-sm">

                @php
                    $linkClass =
                        'flex items-center gap-3 px-3 py-2 rounded-lg transition duration-150 ease-in-out hover:translate-x-0.5 hover:bg-indigo-50';

                    $activeClass = 'text-indigo-700 font-medium';
                    $inactiveClass = 'text-gray-600';

                    $isActive = fn($pattern) => request()->routeIs($pattern);
                @endphp

                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('hr_manager'))
                    <a href="{{ route('dashboard') }}"
                        class="{{ $linkClass }} {{ $isActive('dashboard') ? $activeClass : $inactiveClass }}">
                        <span>🏠</span>
                        <span x-show="open" x-cloak>Dashboard</span>
                    </a>
                    {{-- <a href="{{ route('employees.index') }}"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-indigo-50 hover:text-indigo-700 {{ request()->routeIs('employees.*') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-600' }}">
                        Employees
                    </a> --}}
                    <a href="{{ route('employees.index') }}"
                        class="{{ $linkClass }} {{ $isActive('employees.index') ? $activeClass : $inactiveClass }}">
                        <span>👥</span>
                        <span x-show="open" x-cloak>Employees</span>
                    </a>
                    <a href="{{ route('departments.index') }}"
                        class="{{ $linkClass }} {{ $isActive('departments.*') ? $activeClass : $inactiveClass }}">
                        <span>🏢</span>
                        <span x-show="open" x-cloak>Departments</span>
                    </a>
                    <a href="{{ route('positions.index') }}"
                        class="{{ $linkClass }} {{ $isActive('positions.*') ? $activeClass : $inactiveClass }}">
                        <span>📌</span>
                        <span x-show="open" x-cloak>Positions</span>
                    </a>
                    <a href="{{ route('attendance.index') }}"
                        class="{{ $linkClass }} {{ $isActive('attendance.index') ? $activeClass : $inactiveClass }}">
                        <span>⏱️</span>
                        <span x-show="open" x-cloak>Attendance</span>
                    </a>
                    <a href="{{ route('leaves.index') }}"
                        class="{{ $linkClass }} {{ $isActive('leaves.index') ? $activeClass : $inactiveClass }}">
                        <span>🏖️</span>
                        <span x-show="open" x-cloak>Leave Requests</span>
                    </a>
                    <a href="{{ route('performance-reviews.index') }}"
                        class="{{ $linkClass }} {{ $isActive('performance-reviews.*') ? $activeClass : $inactiveClass }}">
                        <span>📊</span>
                        <span x-show="open" x-cloak>Performance</span>
                    </a>
                    <a href="{{ route('reports.index') }}"
                        class="{{ $linkClass }} {{ $isActive('reports.*') ? $activeClass : $inactiveClass }}">
                        <span>📁</span>
                        <span x-show="open" x-cloak>Reports</span>
                    </a>
                @endif

                @if (auth()->user()->hasRole('admin'))
                    <a href="{{ route('payroll.index') }}"
                        class="{{ $linkClass }} {{ $isActive('payroll.*') ? $activeClass : $inactiveClass }}">
                        <span>💰</span>
                        <span x-show="open" x-cloak>Payroll</span>
                    </a>
                @endif

                @if (auth()->user()->hasRole('employee'))
                    <a href="{{ route('attendance.my') }}"
                        class="{{ $linkClass }} {{ $isActive('attendance.my') ? $activeClass : $inactiveClass }}">
                        <span>⏱️</span>
                        <span x-show="open" x-cloak>My Attendance</span>
                    </a>
                    <a href="{{ route('leaves.my') }}"
                        class="{{ $linkClass }} {{ $isActive('leaves.my') ? $activeClass : $inactiveClass }}">
                        <span>🏖️</span>
                        <span x-show="open" x-cloak>My Leaves</span>
                    </a>
                    <a href="{{ route('payroll.my') }}"
                        class="{{ $linkClass }} {{ $isActive('payroll.my') ? $activeClass : $inactiveClass }}">
                        <span>💵</span>
                        <span x-show="open" x-cloak>My Payslips</span>
                    </a>
                    <a href="{{ route('employee.profile') }}"
                        class="{{ $linkClass }} {{ $isActive('employee.profile') ? $activeClass : $inactiveClass }}">
                        <span>🙍</span>
                        <span x-show="open" x-cloak>My Profile</span>
                    </a>
                @endif
            </nav>

            {{-- User info + logout --}}
            <div class="border-t border-gray-100 px-4 py-4">
                <p class="truncate text-xs text-gray-500" x-show="open" x-cloak>{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="text-xs text-red-500 hover:underline">Sign out</button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex flex-1 flex-col overflow-hidden">
            <header class="border-b border-gray-100 bg-white px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">{{ $header ?? '' }}</div>

                    {{-- Notification Icon --}}
                    <div class="relative">
                        <a href="{{ route('notifications.index') }}"
                            class="relative inline-flex rounded-full p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>

                            {{-- Badge --}}
                            @if (auth()->user()->unreadNotifications->count() > 0)
                                <span
                                    class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
