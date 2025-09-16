<?php

namespace App\Http\Controllers;

use App\Models\ProductionDetailEntry;
use App\Models\ProductionDetailReport;
use Illuminate\Http\Request;

class ProductionDetailEntryController extends Controller
{
    /**
     * Display a listing of entries for a ProductionDetailReport
     */
    public function index(ProductionDetailReport $production_detail_report)
    {
        return response()->json(
            $production_detail_report->entries()->orderBy('day_no')->get()
        );
    }

    /**
     * Store a newly created entry
     */
    public function store(Request $request, ProductionDetailReport $production_detail_report)
    {
        $data = $request->all();

        $validator = validator($data, [
            'production_date' => 'required|date',
            'daily_output' => 'required|integer|min:0',
            'reject_quantity' => 'nullable|integer|min:0',
            'day_no' => 'nullable|integer|min:1',
            'press_no' => 'nullable|string|max:255',
            'temp_setting' => 'nullable|string|max:255',
            'start_time' => 'required',
            'process_duration' => 'nullable|string|max:255',
            'insert_details' => 'nullable|string|max:255',
            'beginning_weight_rawmat' => 'nullable|string|max:255',
            'ending_weight_rawmat' => 'nullable|string|max:255',
            'rawmat_size_salang' => 'nullable|string|max:255',
            'rawmat_weight_salang' => 'nullable|string|max:255',
            'overflow_weight_salang' => 'nullable|string|max:255',
            'actual_finish_product_weight' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id', // <-- added validation for unit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $firstEntry = $production_detail_report->entries()->orderBy('day_no')->first();
        $originalQty = $firstEntry ? $firstEntry->qty_to_be_produced : ($request->input('qty_to_be_produced') ?? 0);

        $lastEntry = $production_detail_report->entries()->latest('day_no')->first();
        $startingBalance = $lastEntry ? $lastEntry->remaining_balance : $originalQty;

        $remainingBalance = max($startingBalance - $validated['daily_output'], 0);

        // Create entry with required qty_to_be_produced
        $entry = $production_detail_report->entries()->create(array_merge($validated, [
            'pressman_id' => auth()->id(),
            'qty_to_be_produced' => $originalQty, // always include this
            'remaining_balance' => $remainingBalance,
        ]));

        return response()->json([
            'message' => 'Entry created successfully',
            'entry' => $entry
        ], 201);
    }


    /**
     * Update an existing entry
     */
    public function update(Request $request, ProductionDetailEntry $production_detail_entry)
    {
        $data = $request->all();

        $validator = validator($data, [
            'production_date' => 'required|date',
            'daily_output' => 'required|integer|min:0',
            'reject_quantity' => 'nullable|integer|min:0',
            'day_no' => 'nullable|integer|min:1',
            'press_no' => 'nullable|string|max:255',
            'temp_setting' => 'nullable|string|max:255',
            'start_time' => 'required',
            'process_duration' => 'nullable|string|max:255',
            'insert_details' => 'nullable|string|max:255',
            'beginning_weight_rawmat' => 'nullable|string|max:255',
            'ending_weight_rawmat' => 'nullable|string|max:255',
            'rawmat_size_salang' => 'nullable|string|max:255',
            'rawmat_weight_salang' => 'nullable|string|max:255',
            'overflow_weight_salang' => 'nullable|string|max:255',
            'actual_finish_product_weight' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'unit_id' => 'nullable|exists:units,id', // <-- added validation for unit
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        $production_detail_entry->update($validated);

        // Recompute remaining_balance for this and all subsequent entries
        $entries = $production_detail_entry->productionDetailReport
            ->entries()
            ->orderBy('day_no')
            ->get();

        $originalQty = $entries->first()->qty_to_be_produced ?? 0;
        $remaining = $originalQty;

        foreach ($entries as $entry) {
            $entry->remaining_balance = max($remaining - $entry->daily_output, 0);
            $entry->save();
            $remaining = $entry->remaining_balance;
        }

        return response()->json([
            'message' => 'Entry updated successfully',
            'entry' => $production_detail_entry
        ], 200);
    }

    /**
     * Delete an entry
     */
    public function destroy(ProductionDetailEntry $production_detail_entry)
    {
        $report = $production_detail_entry->productionDetailReport;

        $production_detail_entry->delete();

        // Recompute remaining_balance for all remaining entries
        $entries = $report->entries()->orderBy('day_no')->get();
        $originalQty = $entries->first()->qty_to_be_produced ?? 0;
        $remaining = $originalQty;

        foreach ($entries as $entry) {
            $entry->remaining_balance = max($remaining - $entry->daily_output, 0);
            $entry->save();
            $remaining = $entry->remaining_balance;
        }

        return response()->json(['success' => true]);
    }
}
