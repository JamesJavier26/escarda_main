<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
    {
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

        $this->merge([
            'sr_id' => auth()->id(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sr_id'         => ['required', 'exists:users,id'],
            'customer_id'   => ['required', 'exists:customers,id'],
            // 'so_no'         => ['required', 'string', 'max:255'],
            'po_no'         => ['nullable', 'string'],
            'pr_no'         => ['nullable', 'string'],
            'attention'         => ['nullable', 'string'],
            'terms'         => ['nullable', 'string'],
            // 'so_status'     => ['required', 'in:draft,pending,approved'],
            'remarks'       => ['nullable', 'string'],
            'delivery_date' => ['required', 'string'],
            'currency_id'   => ['nullable', 'exists:currencies,id'],
            'is_vat'           => ['nullable', 'integer', 'min:0'],
                         
            // ✅ Existing items for update()
            'items' => ['array'],
            'items.*.item_id'     => ['nullable', 'exists:items,id'],
            'items.*.name'        => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity'    => ['required', 'numeric', 'min:1'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.unit_id'     => ['nullable', 'exists:units,id'],

            // ✅ New items for store() or append
            'new_items' => ['array'],
            'new_items.*.item_id'     => ['nullable', 'exists:items,id'],
            'new_items.*.name'        => ['required', 'string'],
            'new_items.*.description' => ['nullable', 'string'],
            'new_items.*.quantity'    => ['required', 'numeric', 'min:1'],
            'new_items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'new_items.*.unit_id'     => ['nullable', 'exists:units,id'],

            // Require at least one item
            'items_or_new' => [
                function ($attribute, $value, $fail) {
                    if (empty($this->items) && empty($this->new_items)) {
                        $fail('You must add at least one item.');
                    }
                }
            ],
            'so_attachment' => ['sometimes', 'array'],
            'so_attachment.*' => ['file', 'max:10240'], // 10 MB each
            'file_labels' => ['sometimes', 'array'],
            'file_labels.*' => ['nullable', 'string', 'max:255'],

        ];
    }
    public function attributes(): array
    {
        return [
            'customer_id' => 'Customer',
            'pr_no'       => 'P.R. number',
            'po_no'       => 'P.O. number',
            'attention'   => 'Attention',
            'delivery_date' => 'Delivery Date',
            'currency_id' => 'Currency',
            'is_vat'      => 'VAT',
            'new_items.*.name'        => 'New Item Name',
            'new_items.*.quantity'    => 'New Item Quantity',
            'new_items.*.unit_price'  => 'New Item Unit Price',
            'items.*.name'            => 'Item Name',
            'items.*.quantity'        => 'Item Quantity',
            'items.*.unit_price'      => 'Item Unit Price',
        ];
    }
}
