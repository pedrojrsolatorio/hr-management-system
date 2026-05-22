<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto space-y-6">

        {{-- Stats cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Active Employees</p>
                <p class="text-3xl font-semibold mt-1">{{ $stats['total_employees'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Present Today</p>
                <p class="text-3xl font-semibold mt-1 text-green-600">{{ $stats['present_today'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">On Leave Today</p>
                <p class="text-3xl font-semibold mt-1 text-amber-600">{{ $stats['on_leave_today'] }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-5">
                <p class="text-sm text-gray-500">Pending Leaves</p>
                <p class="text-3xl font-semibold mt-1 text-red-500">{{ $stats['pending_leaves'] }}</p>
            </div>
        </div>

        {{-- Charts --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-sm font-medium text-gray-600 mb-4">Monthly Attendance</h3>
                <canvas id="attendanceChart" height="200"></canvas>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-sm font-medium text-gray-600 mb-4">Department Distribution</h3>
                <canvas id="deptChart" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-sm font-medium text-gray-600 mb-4">Monthly Payroll Cost</h3>
            <canvas id="payrollChart" height="100"></canvas>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const attendanceData = @json($attendanceData);
        const deptData       = @json($deptData);
        const payrollData    = @json($payrollData);

        new Chart(document.getElementById('attendanceChart'), {
            type: 'bar',
            data: {
                labels: attendanceData.map(d => d.month),
                datasets: [
                    { label: 'Present', data: attendanceData.map(d => d.present), backgroundColor: '#22c55e' },
                    { label: 'Absent',  data: attendanceData.map(d => d.absent),  backgroundColor: '#ef4444' },
                ],
            },
            options: { responsive: true, plugins: { legend: { position: 'top' } } },
        });

        new Chart(document.getElementById('deptChart'), {
            type: 'doughnut',
            data: {
                labels: deptData.map(d => d.name),
                datasets: [{ data: deptData.map(d => d.count), backgroundColor: ['#6366f1','#0ea5e9','#f59e0b','#22c55e','#ec4899'] }],
            },
            options: { responsive: true },
        });

        new Chart(document.getElementById('payrollChart'), {
            type: 'line',
            data: {
                labels: payrollData.map(d => d.month),
                datasets: [{
                    label: 'Net Payroll',
                    data: payrollData.map(d => d.total),
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.08)',
                    fill: true,
                    tension: 0.4,
                }],
            },
            options: { responsive: true, plugins: { legend: { display: false } } },
        });
    </script>
    @endpush
</x-app-layout>