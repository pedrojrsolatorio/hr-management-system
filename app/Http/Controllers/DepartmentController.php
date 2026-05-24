<?php

namespace App\Http\Controllers;

use App\Models\{Department, Employee, User};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Department::withCount('employees')
            ->with('manager')
            ->when(
                $request->filled('search'),
                fn($q) =>
                $q->where('name', 'like', '%' . $request->search . '%')
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create(): View
    {
        $managers = User::whereHas(
            'roles',
            fn($q) =>
            $q->whereIn('slug', ['admin', 'hr_manager'])
        )->pluck('name', 'id');

        return view('departments.create', compact('managers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name',
            'description' => 'nullable|string|max:1000',
            'manager_id'  => 'nullable|exists:users,id',
        ]);

        Department::create($validated);

        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $department->load(['manager', 'employees.user', 'employees.position']);
        $department->loadCount('employees');

        return view('departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $managers = User::whereHas(
            'roles',
            fn($q) =>
            $q->whereIn('slug', ['admin', 'hr_manager'])
        )->pluck('name', 'id');

        return view('departments.edit', compact('department', 'managers'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:departments,name,' . $department->id,
            'description' => 'nullable|string|max:1000',
            'manager_id'  => 'nullable|exists:users,id',
        ]);

        $department->update($validated);

        return redirect()->route('departments.show', $department)
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // Prevent deletion if employees are assigned
        if ($department->employees()->exists()) {
            return back()->with('error', 'Cannot delete a department that has employees assigned.');
        }

        $department->delete();

        return redirect()->route('departments.index')
            ->with('success', 'Department deleted.');
    }
}
