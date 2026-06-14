<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Reports</h2>
    </x-slot>
    <div class="mx-auto grid max-w-4xl grid-cols-1 gap-5 px-6 py-8 md:grid-cols-3">

        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="mb-3 text-sm font-medium text-gray-700">Employee Report</h3>
            <div class="flex gap-2">
                <a href="{{ route('reports.employees') }}?format=pdf" target="_blank"
                    class="rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700 hover:bg-red-100">PDF</a>
                <a href="{{ route('reports.employees') }}?format=excel"
                    class="rounded-lg bg-green-50 px-3 py-2 text-xs text-green-700 hover:bg-green-100">Excel</a>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="mb-3 text-sm font-medium text-gray-700">Payroll Report</h3>
            <form method="GET" action="{{ route('reports.payroll') }}" target="_blank" class="space-y-2">
                <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                    class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                <div class="flex gap-2">
                    <button type="submit" name="format" value="pdf"
                        class="rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700 hover:bg-red-100">PDF</button>
                    <button type="submit" name="format" value="excel"
                        class="rounded-lg bg-green-50 px-3 py-2 text-xs text-green-700 hover:bg-green-100">Excel</button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6">
            <h3 class="mb-3 text-sm font-medium text-gray-700">Attendance Report</h3>
            <form method="GET" action="{{ route('reports.attendance') }}" class="space-y-2">
                <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                    class="w-full rounded-lg border border-gray-200 px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                <button type="submit"
                    class="rounded-lg bg-indigo-50 px-3 py-2 text-xs text-indigo-700 hover:bg-indigo-100">View</button>
            </form>
        </div>
    </div>
</x-app-layout>
