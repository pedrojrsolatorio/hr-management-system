<?php

namespace App\Http\Controllers;

use App\Models\{Employee, Department, Position};
use App\Services\EmployeeService;
use App\Http\Requests\{StoreEmployeeRequest, UpdateEmployeeRequest};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $service) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Employee::class);

        // Using Eloquent relationships + eager loading which prevents the N+1 query problem
        $query = Employee::with(['user', 'department', 'position']);

        // Show soft-deleted records when trashed=1 is in the request
        if ($request->boolean('trashed') || $request->status === 'terminated') {
            $query->onlyTrashed();
        }

        $employees = $query
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->whereHas(
                    'user',
                    fn($u) =>
                    $u->where('name', 'like', '%' . $request->search . '%')
                )
            )
            ->when(
                $request->filled('department'),
                fn($q) =>
                $q->where('department_id', $request->department)
            )
            ->when(
                $request->filled('status'),
                fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $departments = Department::pluck('name', 'id');

        return view('employees.index', compact('employees', 'departments'));
    }

    public function create(): View
    {
        $this->authorize('create', Employee::class);

        $departments = Department::pluck('name', 'id');
        $positions   = Position::pluck('title', 'id');

        return view('employees.create', compact('departments', 'positions'));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $photoPath = null;

        // Handle file upload in controller (HTTP layer)
        if ($request->hasFile('profile_photo')) {
            $photoPath = $request->file('profile_photo')
                ->store('employees', 'public');
            $data['profile_photo'] = $photoPath;
        }

        try {
            $this->service->create($data);
        } catch (\Throwable $th) {
            // Clean up the uploaded file if transaction failed
            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create employee. Please try again.');
        }

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee): View
    {
        // // Removed this line because the before() in EmployeePolicy grants admin+hr_manager automatically.
        // // Employees use the separate profile() method via /my-profile route.
        // $this->authorize('view', $employee);

        $employee->load(['user', 'department', 'position', 'leaveRequests', 'payrolls']);

        $recentAttendance = $employee->attendance()
            ->latest()
            ->limit(10)
            ->get();

        return view('employees.show', compact('employee', 'recentAttendance'));
    }

    public function edit(Employee $employee): View
    {
        $this->authorize('update', $employee);

        $departments = Department::pluck('name', 'id');
        $positions   = Position::pluck('title', 'id');

        return view('employees.edit', compact('employee', 'departments', 'positions'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->service->update($employee, $request->validated());

        return redirect()->route('employees.show', $employee)
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $this->authorize('delete', $employee);

        $this->service->delete($employee);

        return redirect()->route('employees.index')
            ->with('success', 'Employee terminated.');
    }

    // Restore a soft-deleted employee.
    public function restore(int $id): RedirectResponse
    {
        $this->authorize('delete', Employee::class);

        $this->service->restore($id);

        return redirect()->route('employees.index', ['trashed' => 1])
            ->with('success', 'Employee restored successfully.');
    }

    // Permanently delete a soft-deleted employee.
    public function forceDestroy(int $id): RedirectResponse
    {
        $this->authorize('delete', Employee::class);

        $employee = Employee::withTrashed()->findOrFail($id);

        $this->service->forceDelete($employee);

        return redirect()->route('employees.index', ['trashed' => 1])
            ->with('success', 'Employee permanently deleted.');
    }

    public function profile(): View
    {
        $user     = auth()->user();
        $employee = $user->employee;

        $employee->load(['department', 'position', 'attendance', 'leaveRequests']);

        return view('employees.profile', compact('employee'));
    }
}
