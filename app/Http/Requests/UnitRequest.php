<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('unit')?->id;

        return [
            'name'           => ['required', 'max:60', Rule::unique('units','name')->ignore($id)],
            'singular_name'  => ['required', 'max:60', Rule::unique('units','singular_name')->ignore($id)],
            'short_name'     => ['required', 'max:10', Rule::unique('units','short_name')->ignore($id)],
            'type'           => ['nullable', 'max:20'],
            'decimal_places' => ['required', 'integer', 'between:0,6'],
            'description'    => ['nullable'],
            'is_active'      => ['boolean'],
        ];
    }
}
