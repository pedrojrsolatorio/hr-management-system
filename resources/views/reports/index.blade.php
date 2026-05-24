<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Reports</h2>
    </x-slot>
    <div class="py-8 px-6 max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-5">

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Employee Report</h3>
            <div class="flex gap-2">
                <a href="{{ route('reports.employees') }}?format=pdf" target="_blank"
                   class="px-3 py-2 bg-red-50 text-red-700 text-xs rounded-lg hover:bg-red-100">PDF</a>
                <a href="{{ route('reports.employees') }}?format=excel"
                   class="px-3 py-2 bg-green-50 text-green-700 text-xs rounded-lg hover:bg-green-100">Excel</a>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Payroll Report</h3>
            <form method="GET" action="{{ route('reports.payroll') }}" class="space-y-2">
                <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                <div class="flex gap-2">
                    <button type="submit" name="format" value="pdf"
                        class="px-3 py-2 bg-red-50 text-red-700 text-xs rounded-lg hover:bg-red-100">PDF</button>
                    <button type="submit" name="format" value="excel"
                        class="px-3 py-2 bg-green-50 text-green-700 text-xs rounded-lg hover:bg-green-100">Excel</button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Attendance Report</h3>
            <form method="GET" action="{{ route('reports.attendance') }}" class="space-y-2">
                <input type="month" name="month" value="{{ now()->format('Y-m') }}"
                    class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-300" />
                <button type="submit"
                    class="px-3 py-2 bg-indigo-50 text-indigo-700 text-xs rounded-lg hover:bg-indigo-100">View</button>
            </form>
        </div>
    </div>
</x-app-layout>