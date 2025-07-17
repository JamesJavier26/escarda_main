@php
    $editing = isset($employee);
@endphp

<form method="POST"
      action="{{ $editing ? route('employees.update', $employee) : route('employees.store') }}"
      class="space-y-8">

    @csrf
    @if($editing) @method('PUT') @endif

    {{-- IDENTIFICATION --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Identification</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-forms.input
                name="employee_number"
                label="Employee #"
                value="{{ old('employee_number', $employee->employee_number ?? '') }}"
                required />

            <x-forms.select
                name="user_id"
                label="System User"
                :options="$users"
                :selected="old('user_id', $employee->user_id ?? '')"
                placeholder="— No linked user —" />

            <x-forms.select
                name="manager_id"
                label="Immediate Manager"
                :options="$managers"
                :selected="old('manager_id', $employee->manager_id ?? '')"
                placeholder="— None —" />
        </div>
    </div>

    {{-- BASIC DETAILS --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Basic Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-forms.input name="first_name" label="First Name"
                           value="{{ old('first_name', $employee->first_name ?? '') }}"
                           required />
            <x-forms.input name="middle_name" label="Middle Name"
                           value="{{ old('middle_name', $employee->middle_name ?? '') }}" />
            <x-forms.input name="last_name" label="Last Name"
                           value="{{ old('last_name', $employee->last_name ?? '') }}"
                           required />
        </div>
    </div>

    {{-- POSITION --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Position</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-forms.select
                name="department_id"
                label="Department"
                :options="$departments"
                :selected="old('department_id', $employee->department_id ?? '')" />

            <x-forms.select
                name="division_id"
                label="Division"
                :options="$divisions"
                :selected="old('division_id', $employee->division_id ?? '')" />

            <x-forms.select
                name="position_id"
                label="Position"
                :options="$positions"
                :selected="old('position_id', $employee->position_id ?? '')" />
        </div>
    </div>

    {{-- CONTACT --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Contact</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-forms.input type="email" name="email" label="Email"
                           value="{{ old('email', $employee->email ?? '') }}"
                           required />
            <x-forms.input name="phone" label="Phone"
                           value="{{ old('phone', $employee->phone ?? '') }}" />
            <x-forms.input name="street" label="Street Address"
                           value="{{ old('street', $employee->street ?? '') }}" />
        </div>
    </div>

    {{-- EMPLOYMENT DATES --}}
    <div class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Employment Dates</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-forms.input type="date" name="hire_date" label="Hire Date"
                           value="{{ old('hire_date', $employee->hire_date ?? '') }}"
                           required />
            <x-forms.input type="date" name="termination_date" label="Termination Date"
                           value="{{ old('termination_date', $employee->termination_date ?? '') }}" />
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="flex items-center gap-4">
        <x-button>{{ $editing ? 'Save Changes' : 'Create Employee' }}</x-button>
        <a href="{{ route('employees.index') }}"
           class="text-gray-600 dark:text-gray-400 hover:underline">
            Cancel
        </a>
    </div>
</form>
