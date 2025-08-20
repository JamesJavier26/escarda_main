<?php

namespace App\Http\Controllers;

use App\Models\TemporaryQuotation;
use App\Models\Customer;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\User;
use App\Models\Setting;
use Barryvdh\DomPDF\PDF;
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

            // Items (new)
            'new_items' => 'nullable|array',
            'new_items.*.item_id' => 'nullable|exists:items,id',
            'new_items.*.item_name' => 'required|string',       // ✅ fixed
            'new_items.*.item_description' => 'nullable|string',// ✅ fixed
            'new_items.*.remarks' => 'nullable|string',
            'new_items.*.unit_id' => 'nullable|integer',
            'new_items.*.quantity' => 'required|numeric|min:1',
            'new_items.*.unit_price' => 'required|numeric|min:0',
            'new_items.*._delete' => 'nullable|boolean',        // ✅ handle delete flag
        ]);
        $data['created_by'] = auth()->id();

        // Create main quotation
        $temporaryQuotation = TemporaryQuotation::create($data);

        // Attach items
        foreach ($request->input('new_items', []) as $itemData) {
            if (!empty($itemData['_delete'])) {
                continue; // skip deleted
            }

            $temporaryQuotation->items()->create([
                'item_id'          => $itemData['item_id'] ?? null,
                'item_name'        => $itemData['item_name'],           // ✅ fixed
                'item_description' => $itemData['item_description'] ?? null,
                'remarks'          => $itemData['remarks'] ?? null,
                'unit_id'          => $itemData['unit_id'] ?? null,
                'quantity'         => $itemData['quantity'],
                'unit_price'       => $itemData['unit_price'],
            ]);
        }

        return redirect()
            ->route('temporary_quotations.index')
            ->with('success', 'Temporary Quotation created successfully.');
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

            // Existing items
            'items' => 'nullable|array',
            'items.*.id' => 'nullable|exists:temporary_quotation_items,id',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'required|string',
            'items.*.item_description' => 'nullable|string',
            'items.*.remarks' => 'nullable|string',
            'items.*.unit_id' => 'nullable|integer',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*._delete' => 'nullable|boolean',

            // New items
            'new_items' => 'nullable|array',
            'new_items.*.item_id' => 'nullable|exists:items,id',
            'new_items.*.item_name' => 'required|string',
            'new_items.*.item_description' => 'nullable|string',
            'new_items.*.remarks' => 'nullable|string',
            'new_items.*.unit_id' => 'nullable|integer',
            'new_items.*.quantity' => 'required|numeric|min:1',
            'new_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Update main record (excluding items arrays)
        $temporaryQuotation->update(
            collect($data)->except(['items','new_items','created_by'])->toArray()
        );

        // Handle existing items
        foreach ($request->input('items', []) as $itemData) {
            if (!empty($itemData['_delete'])) {
                $temporaryQuotation->items()->where('id', $itemData['id'])->delete();
                continue;
            }
            $item = $temporaryQuotation->items()->find($itemData['id']);
            if ($item) {
                $item->update([
                    'item_id'          => $itemData['item_id'] ?? null,
                    'item_name'        => $itemData['item_name'],
                    'item_description' => $itemData['item_description'] ?? null,
                    'remarks'          => $itemData['remarks'] ?? null,
                    'unit_id'          => $itemData['unit_id'] ?? null,
                    'quantity'         => $itemData['quantity'],
                    'unit_price'       => $itemData['unit_price'],
                ]);
            }
        }

        // Handle new items
        foreach ($request->input('new_items', []) as $itemData) {
            if (!empty($itemData['_delete'])) continue;

            $temporaryQuotation->items()->create([
                'item_id'          => $itemData['item_id'] ?? null,
                'item_name'        => $itemData['item_name'],
                'item_description' => $itemData['item_description'] ?? null,
                'remarks'          => $itemData['remarks'] ?? null,
                'unit_id'          => $itemData['unit_id'] ?? null,
                'quantity'         => $itemData['quantity'],
                'unit_price'       => $itemData['unit_price'],
            ]);
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

    public function generateTemporaryQuote(PDF $pdf, $id)
    {
        $temporaryQuotation = TemporaryQuotation::with([
            'customer',
            'salesRep',
            'items.unit',
            'currency'
        ])->findOrFail($id);

        $data = [
            'temporaryQuotation' => $temporaryQuotation,
            'date'               => now()->format('F d, Y'),
            'setting'            => Setting::all(),
        ];

        $pdf->loadView('reports.generateTemporaryQuote', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('TemporaryQuotation.pdf');
    }
}
