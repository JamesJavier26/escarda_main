<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $rules = [
            'employee_number' => ['required', 'string', 'max:50'],
            'user_id'   => 'nullable|exists:users,id',
            'department_id'   => 'required|exists:departments,id',
            'division_id'     => 'nullable|exists:divisions,id',
            'position_id'     => 'required|exists:positions,id',
            'manager_id'      => 'nullable|exists:employees,id',
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'prefix' => ['nullable', 'string', 'max:10'],
            'middle_name' => ['nullable', 'string', 'max:50'],
            'suffix' => ['nullable', 'string', 'in:'.implode(',', array_keys(config('selectOptions.suffixes')))],
            'professional_title' => ['nullable', 'string', 'max:10'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'province_id' => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'string', 'max:255'],
            'barangay_id' => ['nullable', 'string', 'max:255'],
            'region_id' => ['nullable', 'string', 'max:255'],
            'zipcode' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'hire_date' => ['required', 'date'],
            'termination_date' => ['nullable', 'date', 'after:hire_date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'marital_status' => ['nullable', 'in:single,married,separated,widowed'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'employment_type' => ['nullable', 'string'],
            'job_status' => ['nullable', 'string'],
            'emergency_contact_name' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:50'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'social_security_no' => ['nullable', 'string', 'max:50'],
            'philhealth_no' => ['nullable', 'string', 'max:50'],
            'pagibig_no' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];

        // Add unique rule for employee_number on create
        if ($this->isMethod('post')) {
            $rules['employee_number'][] = 'unique:employees';
        }
        // Add unique rule for employee_number on update
        else {
            $rules['employee_number'][] = 'unique:employees,employee_number,'.$this->employee->id;
        }

        return $rules;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert empty strings to null for nullable fields
        $this->merge([
            'suffix' => $this->suffix ?: null,
            'province_id' => $this->province_id ?: null,
            'city_id' => $this->city_id ?: null,
            'barangay_id' => $this->barangay_id ?: null,
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'termination_date.after' => 'The termination date must be after the hire date.',
            'suffix.in' => 'The selected suffix is invalid.',
        ];
    }
}