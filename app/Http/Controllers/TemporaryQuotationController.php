<?php

namespace App\Http\Controllers;

use App\Models\TemporaryQuotation;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;

class TemporaryQuotationController extends Controller
{
    public function index()
    {
        $temporaryQuotations = TemporaryQuotation::with(['customer', 'currency', 'salesRep'])->latest()->paginate(10);
        return view('temporary_quotations.index', compact('temporaryQuotations'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $currencies = Currency::orderBy('name')->get();
        $users = User::orderBy('username')->get();
        $units = Unit::orderBy('name')->get();

        $temporary_quotation = new TemporaryQuotation();
        return view('temporary_quotations.create', compact('customers', 'currencies', 'users', 'units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'sr_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'attention' => 'required|string|max:255',
            'terms' => 'required|string|max:255',
            'pr_no' => 'required|string|max:255',
            'delivery_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'vat' => 'required|string|max:50',
            'pe_remarks' => 'nullable|string',
            'sr_remarks' => 'nullable|string',
            'currency_id' => 'nullable|exists:currencies,id',
            'new_items' => 'nullable|array',
            'new_items.*.item_id' => 'nullable|exists:items,id',
            'new_items.*.name' => 'required|string',
            'new_items.*.description' => 'nullable|string',
            'new_items.*.remarks' => 'nullable|string',
            'new_items.*.unit_id' => 'nullable|integer',
            'new_items.*.quantity' => 'required|numeric|min:1',
            'new_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $temporaryQuotation = TemporaryQuotation::create($data);

        foreach ($request->input('new_items', []) as $itemData) {
            if (!empty($itemData['_delete'])) continue;

            $temporaryQuotation->items()->create([
                'item_id' => $itemData['item_id'],
                'item_name' => $itemData['name'],
                'item_description' => $itemData['description'] ?? null,
                'remarks' => $itemData['remarks'] ?? null,
                'unit_id' => $itemData['unit_id'] ?? null,
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
            ]);
        }

        return redirect()->route('temporary_quotations.index')->with('success', 'Temporary Quotation created successfully.');
    }

    public function show(TemporaryQuotation $temporaryQuotation)
    {
        return view('temporary_quotations.show', compact('temporaryQuotation'));
    }

    public function edit(TemporaryQuotation $temporaryQuotation)
    {
        $customers = Customer::orderBy('name')->get();
        $currencies = Currency::orderBy('name')->get();
        $users = User::orderBy('username')->get();
        $units = Unit::orderBy('name')->get();

        return view('temporary_quotations.edit', compact('temporaryQuotation', 'customers', 'currencies', 'users', 'units'));
    }

    public function update(Request $request, TemporaryQuotation $temporaryQuotation)
    {
        $validated = $request->validate([
            'sr_id' => 'nullable|exists:users,id',
            'customer_id' => 'nullable|exists:customers,id',
            'attention' => 'required|string|max:255',
            'terms' => 'required|string|max:255',
            'pr_no' => 'required|string|max:255',
            'delivery_date' => 'nullable|date',
            'remarks' => 'nullable|string',
            'vat' => 'required|string|max:50',
            'pe_remarks' => 'nullable|string',
            'sr_remarks' => 'nullable|string',
            'currency_id' => 'nullable|exists:currencies,id',

            // Validation for items
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:temporary_quotation_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_id' => 'nullable|exists:units,id',
            'items.*.item_description' => 'nullable|string',
            'items.*.pe_remarks' => 'nullable|string',
            'items.*.final_unit_price' => 'nullable|numeric|min:0',
        ]);

        $temporaryQuotation->update($validated);

        // Handle updating items
        if (isset($validated['items'])) {
            // Get all existing item IDs
            $existingIds = $temporaryQuotation->items()->pluck('id')->toArray();

            $submittedIds = collect($validated['items'])->pluck('id')->filter()->toArray();

            // Delete removed items
            $itemsToDelete = array_diff($existingIds, $submittedIds);
            if (!empty($itemsToDelete)) {
                $temporaryQuotation->items()->whereIn('id', $itemsToDelete)->delete();
            }

            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing
                    $item = $temporaryQuotation->items()->find($itemData['id']);
                    if ($item) {
                        $item->update($itemData);
                    }
                } else {
                    // Create new
                    $temporaryQuotation->items()->create($itemData);
                }
            }
        }

        return redirect()->route('temporary_quotations.index')
            ->with('success', 'Temporary Quotation updated successfully.');
    }

    public function destroy(TemporaryQuotation $temporaryQuotation)
    {
        $temporaryQuotation->delete();
        return redirect()->route('temporary_quotations.index')
            ->with('success', 'Temporary Quotation deleted successfully.');
    }
}
