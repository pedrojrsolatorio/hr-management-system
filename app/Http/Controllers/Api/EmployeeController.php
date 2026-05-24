<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmployeeResource;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeController extends Controller
{
    public function __construct(private EmployeeService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $employees = Employee::with(['user', 'department', 'position'])
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"))
            )
            ->when(
                $request->department_id,
                fn($q) =>
                $q->where('department_id', $request->department_id)
            )
            ->when(
                $request->status,
                fn($q) =>
                $q->where('status', $request->status)
            )
            ->paginate(20);

        return EmployeeResource::collection($employees);
    }

    public function store(StoreEmployeeRequest $request): JsonResponse
    {
        $employee = $this->service->create($request->validated());
        return response()->json(new EmployeeResource($employee->load('user', 'department', 'position')), 201);
    }

    public function show(Employee $employee): EmployeeResource
    {
        return new EmployeeResource(
            $employee->load(['user', 'department', 'position', 'attendance', 'leaveRequests'])
        );
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): EmployeeResource
    {
        $updated = $this->service->update($employee, $request->validated());
        return new EmployeeResource($updated->load('user', 'department', 'position'));
    }

    public function destroy(Employee $employee): JsonResponse
    {
        $this->service->delete($employee);
        return response()->json(['message' => 'Employee deleted.']);
    }
}
