<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PricingEstimateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Handle JSON strings if passed
        if (is_string($this->items)) {
            $this->merge([
                'items' => json_decode($this->items, true) ?? [],
            ]);
        }

        if (is_string($this->new_items)) {
            $this->merge([
                'new_items' => json_decode($this->new_items, true) ?? [],
            ]);
        }

        $status = $this->input('action') === 'pending' ? 'pending' : 'draft';

        $this->merge([
            'sr_id' => $this->input('sr_id') ?? auth()->id(),
            'pe_status' => $status,
        ]);
    }

    public function rules(): array
    {
        $pricingEstimateId = $this->route('pricing_estimate')?->id;

        return [
            'pe_no' => [
                'required',
                'string',
                'max:255',
                Rule::unique('pricing_estimates', 'pe_no')->ignore($pricingEstimateId),
            ],
            'sr_id'         => ['required', 'exists:users,id'],
            'customer_id'   => ['required', 'exists:customers,id'],
            'attention'     => ['required', 'string', 'max:255'],
            'terms'         => ['required', 'string', 'max:255'],
            'pr_no'         => ['required', 'string', 'max:255'],
            'delivery_date' => ['nullable', 'string'],
            'remarks'       => ['nullable', 'string'],
            'pe_status'     => ['required', Rule::in(['draft', 'pending', 'approved'])],
            'vat'           => ['nullable', 'integer', 'min:0'],
            'pe_remarks'    => ['nullable', 'string'],
            'sr_remarks'    => ['nullable', 'string'],
            'currency_id'   => ['nullable', 'exists:currencies,id'],

            // Validate both existing and new items
            'items' => ['array'],
            'items.*.item_id' => ['nullable', 'exists:items,id'],
            'items.*.name' => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.unit_id' => ['nullable', 'exists:units,id'],
            'items.*.pe_remarks' => ['nullable', 'string'],
            'items.*.notes' => ['nullable', 'string'],

            'new_items' => ['array'],
            'new_items.*.item_id' => ['nullable', 'exists:items,id'],
            'new_items.*.name' => ['required', 'string'],
            'new_items.*.description' => ['nullable', 'string'],
            'new_items.*.quantity' => ['required', 'numeric', 'min:1'],
            'new_items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'new_items.*.unit_id' => ['nullable', 'exists:units,id'],
            'new_items.*.pe_remarks' => ['nullable', 'string'],
            'new_items.*.notes' => ['nullable', 'string'],

            'items_or_new' => [
                function ($attribute, $value, $fail) {
                    if (empty($this->items) && empty($this->new_items)) {
                        $fail('You must add at least one item.');
                    }
                }
            ],

        'pe_attachment' => ['sometimes', 'array'],
        'pe_attachment.*' => ['file', 'max:10240'], // 10 MB each
        'file_labels' => ['sometimes', 'array'],
        'file_labels.*' => ['nullable', 'string', 'max:255'],

        ];
    }

    public function attributes(): array
    {
        return [
            'customer_id' => 'Customer',
            'pe_no' => 'P.E. number',
            'sr_id' => 'Sales Representative',
            'pr_no' => 'P.R. number',
            'vat' => 'VAT',
            'items' => 'items list',
            'items.*.quantity' => 'item quantity',
            'items.*.unit_price' => 'unit price',
            'new_items.*.quantity' => 'new item quantity',
            'new_items.*.unit_price' => 'new item unit price',
        ];
    }
}
