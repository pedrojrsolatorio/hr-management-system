<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::with('employee.user')->latest('date');

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $employees   = Employee::with('user')->where('status', 'active')->get();

        return view('attendance.index', compact('attendances', 'employees'));
    }

    public function myAttendance(): View
    {
        /** @var User $user */
        $user        = auth()->user();
        $attendances = Attendance::where('employee_id', $user->employee->id)
            ->latest('date')
            ->paginate(20);

        return view('attendance.my', compact('attendances'));
    }

    public function checkIn(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user     = auth()->user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile linked to your account.');
        }

        $alreadyCheckedIn = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->exists();

        if ($alreadyCheckedIn) {
            return back()->with('error', 'You have already checked in today.');
        }

        $lateThreshold = Carbon::today()->setTime(9, 0);
        $status        = now()->greaterThan($lateThreshold) ? 'late' : 'present';

        Attendance::create([
            'employee_id' => $employee->id,
            'date'        => today()->toDateString(),
            'check_in'    => now()->format('H:i:s'),
            'status'      => $status,
        ]);

        return back()->with('success', 'Checked in at ' . now()->format('H:i'));
    }

    public function checkOut(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user       = auth()->user();
        $employee   = $user->employee;

        if (!$employee) {
            return back()->with('error', 'No employee profile linked to your account.');
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today()->toDateString())
            ->first();

        if (!$attendance) {
            return back()->with('error', 'You have not checked in today.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'You have already checked out today.');
        }

        $attendance->update(['check_out' => now()->format('H:i:s')]);

        return back()->with('success', 'Checked out at ' . now()->format('H:i'));
    }

    public function report(Request $request): View
    {
        $month = $request->get('month', now()->format('Y-m'));

        // Parse month safely — no whereRaw needed
        [$year, $mon] = explode('-', $month);
        $start = Carbon::create((int) $year, (int) $mon, 1)->startOfMonth()->toDateString();
        $end   = Carbon::create((int) $year, (int) $mon, 1)->endOfMonth()->toDateString();

        $employees = Employee::with([
            'user',
            'attendance' => fn($q) => $q->whereBetween('date', [$start, $end]),
        ])
            ->where('status', 'active')
            ->get();

        $summary = $employees->map(function (Employee $employee) {
            $att = $employee->attendance;

            return [
                'employee' => $employee->user->name,
                'code'     => $employee->employee_code,
                'present'  => $att->where('status', 'present')->count(),
                'late'     => $att->where('status', 'late')->count(),
                'absent'   => $att->where('status', 'absent')->count(),
                'total'    => $att->count(),
            ];
        });

        return view('attendance.report', compact('summary', 'month'));
    }
}
