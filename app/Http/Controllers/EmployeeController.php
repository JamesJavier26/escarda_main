<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeRequest;
use App\Models\Department;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\Address\Entities\Province;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        // Validate perPage
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        // Base query with relationships
        $query = Employee::with(['department', 'position', 'user']);

        // Search by name, employee number, or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('employee_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($sub) => $sub->where('email', 'like', "%{$search}%"));
            });
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        // Filter by position
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->input('position_id'));
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        // Paginate with appends for query strings
        $employees = $query->latest('created_at')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Filter options
        $departments = Department::orderBy('name')->get();
        $positions = Position::orderBy('title')->get();

        return view('employees.index', compact('employees', 'departments', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         return view('employees.create', [
            'employee' => new Employee(),
            'provinces' =>  Province::all(),
            'divisions' => Division::all(),
            'departments' => Department::all(),
            'positions' => Position::all(),
            'managers' => null,
            'users' => User::whereDoesntHave('employee')->get()
         ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeRequest $request)
    {
        $employee = Employee::create($request->validated());

        activity()
        ->performedOn($employee)
        ->causedBy(auth()->user())
        ->withProperties([
            'attributes' => $employee->toArray(), 
        ])
        ->useLog('employee')
        ->log('Created employee');
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {

        return view('employees.edit', [
            'employee' => $employee,
            'provinces' =>  Province::all(),
            'divisions' => Division::all(),
            'departments' => Department::all(),
            'positions' => Position::all(),
            'managers' => null,
            'users' =>  User::whereDoesntHave('employee')
            ->orWhere('id', $employee->user_id) 
            ->get()
         ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $employee->update($request->validated());

        activity()
            ->performedOn($employee)
            ->causedBy(auth()->user())
            ->withProperties(['changes' => $request->all()])
            ->useLog('employee')
            ->log('Updated employee');
            
        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        //
    }
     private function validated(Request $request, $id = null): array
    {
        $uniqueEmail = 'unique:employees,email' . ($id ? ",$id" : '');
        $uniqueEmpNo = 'unique:employees,employee_number' . ($id ? ",$id" : '');

        return $request->validate([
            'employee_number'        => ["required", "string", $uniqueEmpNo],
            'user_id'                => ['nullable', 'exists:users,id'],
            'department_id'          => ['nullable', 'exists:departments,id'],
            'division_id'            => ['nullable', 'exists:divisions,id'],
            'position_id'            => ['nullable', 'exists:positions,id'],
            'manager_id'             => ['nullable', 'exists:employees,id'],

            'prefix'                 => ['nullable', 'string', 'max:10'],
            'first_name'             => ['required', 'string', 'max:50'],
            'middle_name'            => ['nullable', 'string', 'max:50'],
            'last_name'              => ['required', 'string', 'max:50'],
            'suffix'                 => ['nullable', 'string', 'max:10'],
            'professional_title'     => ['nullable', 'string', 'max:10'],

            'email'                  => ["required", "email", $uniqueEmail],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'street'                 => ['nullable', 'string', 'max:255'],
            'zipcode'                => ['nullable', 'string', 'max:10'],

            'birth_date'             => ['nullable', 'date'],
            'hire_date'              => ['required', 'date'],
            'termination_date'       => ['nullable', 'date', 'after:hire_date'],

            'gender'                 => ['nullable', 'in:male,female,other'],
            'marital_status'         => ['nullable', 'in:single,married,separated,widowed'],

            'salary'                 => ['nullable', 'numeric', 'min:0'],
            'employment_type'        => ['nullable', 'string', 'max:30'],
            'job_status'             => ['nullable', 'string', 'max:30'],

            'emergency_contact_name'     => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone'    => ['nullable', 'string', 'max:30'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],

            'tax_id'            => ['nullable', 'string', 'max:30'],
            'social_security_no'=> ['nullable', 'string', 'max:30'],
            'philhealth_no'     => ['nullable', 'string', 'max:30'],
            'pagibig_no'        => ['nullable', 'string', 'max:30'],

            'is_active'         => ['boolean'],
        ]);
    }

    private function formLookups(array $extra = []): array
    {
        return array_merge($extra, [
            'users'       => User::orderBy('username')->pluck('username', 'id'),
            'departments' => Department::orderBy('name')->pluck('name', 'id'),
            'divisions'   => Division::orderBy('name')->pluck('name', 'id'),
            'positions'   => Position::orderBy('title')->pluck('title', 'id'),
            'managers'    => Employee::orderBy('first_name')->pluck('first_name', 'id'),
        ]);
    }
}
