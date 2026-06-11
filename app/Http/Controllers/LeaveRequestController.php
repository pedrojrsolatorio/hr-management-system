<?php

namespace App\Http\Controllers;

use App\Models\{LeaveRequest, LeaveType, User};
use App\Http\Requests\StoreLeaveRequest;
use App\Notifications\{LeaveRequestNotification, LeaveStatusNotification};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    public function index(): View
    {
        $leaves = LeaveRequest::with(['employee.user', 'leaveType'])
            ->latest()
            ->paginate(20);

        return view('leaves.index', compact('leaves'));
    }

    public function myLeaves(): View
    {
        $user   = auth()->user();
        // $user = Auth::user(); // recommended to avoid 'Undefined method 'user'.' warning
        $leaves = LeaveRequest::where('employee_id', $user->employee->id)
            ->with('leaveType')
            ->latest()
            ->paginate(20);

        $leaveTypes = LeaveType::all();

        return view('leaves.my', compact('leaves', 'leaveTypes'));
    }

    public function store(StoreLeaveRequest $request): RedirectResponse
    {
        $user      = auth()->user();
        $employee  = $user->employee;
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $days      = $startDate->diffInWeekdays($endDate) + 1;

        // Check leave balance
        $leaveType  = LeaveType::findOrFail($request->leave_type_id);
        $usedDays   = LeaveRequest::where('employee_id', $employee->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('total_days');

        if (($usedDays + $days) > $leaveType->days_allowed) {
            return back()->withErrors(['end_date' => 'Insufficient leave balance.'])->withInput();
        }

        $leave = LeaveRequest::create([
            'employee_id'   => $employee->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date'    => $request->start_date,
            'end_date'      => $request->end_date,
            'total_days'    => $days,
            'reason'        => $request->reason,
            'status'        => 'pending',
        ]);

        // Notify HR managers
        User::whereHas('roles', fn($q) => $q->where('slug', 'hr_manager'))
            ->each(fn(User $u) => $u->notify(new LeaveRequestNotification($leave)));

        return redirect()->route('leaves.my')->with('success', 'Leave request submitted.');
    }

    public function approve(LeaveRequest $leave): RedirectResponse
    {
        $leave->update([
            'status'      => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $leave->employee->user->notify(new LeaveStatusNotification($leave));

        return back()->with('success', 'Leave approved.');
    }

    public function reject(Request $request, LeaveRequest $leave): RedirectResponse
    {
        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $leave->update([
            'status'           => 'rejected',
            'approved_by'      => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $leave->employee->user->notify(new LeaveStatusNotification($leave));

        return back()->with('success', 'Leave rejected.');
    }
}
