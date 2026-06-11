<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MarkAbsentEmployees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:mark-absent-employees';
    protected $signature = 'attendance:mark-absent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark employees with no attendance record for today as absent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Only run on weekdays
        if (Carbon::today()->isWeekend()) {
            $this->info('Weekend — skipping absent marking.');
            return;
        }

        $today = today()->toDateString();

        // // Get all active employees
        // $allEmployeeIds = Employee::where('status', 'active')->pluck('id');

        // // Get employees who already have a record for today
        // $checkedInIds = Attendance::whereDate('date', $today)->pluck('employee_id');

        // // The difference = absent employees
        // $absentIds = $allEmployeeIds->diff($checkedInIds);

        // better single query than the commented 3 lines above
        $absentIds = Employee::where('status', 'active')
            ->whereDoesntHave('attendance', function ($q) use ($today) {
                $q->whereDate('date', $today);
                // ->whereNotNull('check_in'); // did not include this because it can duplicate record since absent still has no check_in
            })
            ->pluck('id');

        $count = 0;
        foreach ($absentIds as $employeeId) {
            Attendance::create([
                'employee_id' => $employeeId,
                'date'        => $today,
                'check_in'    => null,
                'check_out'   => null,
                'status'      => 'absent',
            ]);
            $count++;
        }

        $this->info("Marked {$count} employee(s) as absent for {$today}.");
    }
}
