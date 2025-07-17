@props(['employee', 'departments', 'divisions', 'positions', 'managers'])

@php
    $editing = $employee->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('employees.update', $employee) : route('employees.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            {{-- Employee Number --}}
            <div class="mb-6">
                <label for="employee_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Employee Number <span class="text-red-500">*</span>
                </label>
                <input id="employee_number" name="employee_number" type="text"
                    value="{{ old('employee_number', $employee->employee_number) }}"
                    placeholder="Enter employee number"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                         border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                         focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('employee_number')
                    ])>
                @error('employee_number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Name Section --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                {{-- Prefix --}}
                <div>
                    <label for="prefix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Prefix
                    </label>
                    <input id="prefix" name="prefix" type="text"
                        value="{{ old('prefix', $employee->prefix) }}"
                        placeholder="Mr/Ms/Dr"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- First Name --}}
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input id="first_name" name="first_name" type="text"
                        value="{{ old('first_name', $employee->first_name) }}"
                        placeholder="First name"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('first_name')
                        ])>
                    @error('first_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Middle Name --}}
                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Middle Name
                    </label>
                    <input id="middle_name" name="middle_name" type="text"
                        value="{{ old('middle_name', $employee->middle_name) }}"
                        placeholder="Middle name"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Last Name --}}
                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input id="last_name" name="last_name" type="text"
                        value="{{ old('last_name', $employee->last_name) }}"
                        placeholder="Last name"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('last_name')
                        ])>
                    @error('last_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Suffix & Title --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Suffix --}}
                <div>
                    <label for="suffix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Suffix
                    </label>
                    <input id="suffix" name="suffix" type="text"
                        value="{{ old('suffix', $employee->suffix) }}"
                        placeholder="Jr/Sr/III"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Professional Title --}}
                <div>
                    <label for="professional_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Professional Title
                    </label>
                    <input id="professional_title" name="professional_title" type="text"
                        value="{{ old('professional_title', $employee->professional_title) }}"
                        placeholder="e.g. CPA, MD"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email"
                        value="{{ old('email', $employee->email) }}"
                        placeholder="Email address"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('email')
                        ])>
                    @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Phone
                    </label>
                    <input id="phone" name="phone" type="text"
                        value="{{ old('phone', $employee->phone) }}"
                        placeholder="Phone number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Address --}}
            <div class="mb-6">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Full Address
                </label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                           border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:border-transparent">{{ old('address', $employee->address) }}</textarea>
            </div>

            {{-- Street and Zipcode --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Street --}}
                <div>
                    <label for="street" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Street
                    </label>
                    <input id="street" name="street" type="text"
                        value="{{ old('street', $employee->street) }}"
                        placeholder="Street address"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Zipcode --}}
                <div>
                    <label for="zipcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Zipcode
                    </label>
                    <input id="zipcode" name="zipcode" type="text"
                        value="{{ old('zipcode', $employee->zipcode) }}"
                        placeholder="Zip/postal code"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Dates Section --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Birth Date --}}
                <div>
                    <label for="birth_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Birth Date
                    </label>
                    <input id="birth_date" name="birth_date" type="date"
                        value="{{ old('birth_date', $employee->birth_date ? $employee->birth_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Hire Date --}}
                <div>
                    <label for="hire_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Hire Date <span class="text-red-500">*</span>
                    </label>
                    <input id="hire_date" name="hire_date" type="date"
                        value="{{ old('hire_date', $employee->hire_date ? $employee->hire_date->format('Y-m-d') : '') }}"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('hire_date')
                        ])>
                    @error('hire_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Termination Date --}}
                <div>
                    <label for="termination_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Termination Date
                    </label>
                    <input id="termination_date" name="termination_date" type="date"
                        value="{{ old('termination_date', $employee->termination_date ? $employee->termination_date->format('Y-m-d') : '') }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Personal Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Gender --}}
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Gender
                    </label>
                    <select id="gender" name="gender"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select gender</option>
                        <option value="male" @selected(old('gender', $employee->gender) == 'male')>Male</option>
                        <option value="female" @selected(old('gender', $employee->gender) == 'female')>Female</option>
                        <option value="other" @selected(old('gender', $employee->gender) == 'other')>Other</option>
                    </select>
                </div>

                {{-- Marital Status --}}
                <div>
                    <label for="marital_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Marital Status
                    </label>
                    <select id="marital_status" name="marital_status"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select status</option>
                        <option value="single" @selected(old('marital_status', $employee->marital_status) == 'single')>Single</option>
                        <option value="married" @selected(old('marital_status', $employee->marital_status) == 'married')>Married</option>
                        <option value="separated" @selected(old('marital_status', $employee->marital_status) == 'separated')>Separated</option>
                        <option value="widowed" @selected(old('marital_status', $employee->marital_status) == 'widowed')>Widowed</option>
                    </select>
                </div>
            </div>

            {{-- Employment Details --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Salary --}}
                <div>
                    <label for="salary" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Salary
                    </label>
                    <input id="salary" name="salary" type="number" step="0.01"
                        value="{{ old('salary', $employee->salary) }}"
                        placeholder="0.00"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Employment Type --}}
                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Employment Type
                    </label>
                    <select id="employment_type" name="employment_type"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select type</option>
                        <option value="full-time" @selected(old('employment_type', $employee->employment_type) == 'full-time')>Full-time</option>
                        <option value="part-time" @selected(old('employment_type', $employee->employment_type) == 'part-time')>Part-time</option>
                        <option value="contract" @selected(old('employment_type', $employee->employment_type) == 'contract')>Contract</option>
                        <option value="temporary" @selected(old('employment_type', $employee->employment_type) == 'temporary')>Temporary</option>
                        <option value="intern" @selected(old('employment_type', $employee->employment_type) == 'intern')>Intern</option>
                    </select>
                </div>

                {{-- Job Status --}}
                <div>
                    <label for="job_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Job Status
                    </label>
                    <select id="job_status" name="job_status"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select status</option>
                        <option value="active" @selected(old('job_status', $employee->job_status) == 'active')>Active</option>
                        <option value="on-leave" @selected(old('job_status', $employee->job_status) == 'on-leave')>On Leave</option>
                        <option value="suspended" @selected(old('job_status', $employee->job_status) == 'suspended')>Suspended</option>
                        <option value="terminated" @selected(old('job_status', $employee->job_status) == 'terminated')>Terminated</option>
                    </select>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Emergency Contact Name --}}
                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Emergency Contact Name
                    </label>
                    <input id="emergency_contact_name" name="emergency_contact_name" type="text"
                        value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}"
                        placeholder="Contact name"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Emergency Contact Phone --}}
                <div>
                    <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Emergency Contact Phone
                    </label>
                    <input id="emergency_contact_phone" name="emergency_contact_phone" type="text"
                        value="{{ old('emergency_contact_phone', $employee->emergency_contact_phone) }}"
                        placeholder="Contact phone"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Emergency Contact Relation --}}
                <div>
                    <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Relationship
                    </label>
                    <input id="emergency_contact_relation" name="emergency_contact_relation" type="text"
                        value="{{ old('emergency_contact_relation', $employee->emergency_contact_relation) }}"
                        placeholder="Relationship"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Government IDs --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Tax ID --}}
                <div>
                    <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Tax ID
                    </label>
                    <input id="tax_id" name="tax_id" type="text"
                        value="{{ old('tax_id', $employee->tax_id) }}"
                        placeholder="Tax identification number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Social Security No --}}
                <div>
                    <label for="social_security_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Social Security No.
                    </label>
                    <input id="social_security_no" name="social_security_no" type="text"
                        value="{{ old('social_security_no', $employee->social_security_no) }}"
                        placeholder="SSS number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Philhealth No --}}
                <div>
                    <label for="philhealth_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Philhealth No.
                    </label>
                    <input id="philhealth_no" name="philhealth_no" type="text"
                        value="{{ old('philhealth_no', $employee->philhealth_no) }}"
                        placeholder="Philhealth number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Pagibig No --}}
            <div class="mb-6">
                <label for="pagibig_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Pag-IBIG No.
                </label>
                <input id="pagibig_no" name="pagibig_no" type="text"
                    value="{{ old('pagibig_no', $employee->pagibig_no) }}"
                    placeholder="Pag-IBIG number"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                           border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Organizational Relationships --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Department --}}
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Department
                    </label>
                    <select id="department_id" name="department_id"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" @selected(old('department_id', $employee->department_id) == $department->id)>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Division --}}
                <div>
                    <label for="division_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Division
                    </label>
                    <select id="division_id" name="division_id"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select division</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" @selected(old('division_id', $employee->division_id) == $division->id)>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Position --}}
                <div>
                    <label for="position_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Position
                    </label>
                    <select id="position_id" name="position_id"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" disabled selected>Select position</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" @selected(old('position_id', $employee->position_id) == $position->id)>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Manager --}}
                {{-- <div>
                    <label for="manager_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Manager
                    </label>
                    <select id="manager_id" name="manager_id"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                        <option value="" selected>No manager</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" @selected(old('manager_id', $employee->manager_id) == $manager->id)>
                                {{ $manager->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div> --}}
            </div>

            {{-- User Account --}}
            <div class="mb-6">
                <label for="user_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    User Account
                </label>
                <select id="user_id" name="user_id"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                           border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:border-transparent">
                    <option value="" selected>No user account</option>
                    {{-- You would populate this with users from your database --}}
                </select>
            </div>

            {{-- Status --}}
            <div class="mb-6">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Active Status</span>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1"
                            @checked(old('is_active', $employee->is_active) === 1)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0"
                            @checked(old('is_active', $employee->is_active) === 0)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('employees.index') }}"
           class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                  text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                  border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                  focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </a>
        <button type="submit"
                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                       bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{ $editing ? 'Update' : 'Save' }}
        </button>
    </div>
</form>