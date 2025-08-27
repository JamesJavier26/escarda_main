<?php

namespace App\Http\Controllers;

use App\Models\ProductionDetailEntry;
use App\Models\ProductionDetailReport;
use Illuminate\Http\Request;

class ProductionDetailEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ProductionDetailReport $production_detail_report)
    {
        return response()->json($production_detail_report->entries()->latest()->get());
    }
    /**
     * Show the form for creating a new resource.
     */
   public function store(Request $request, ProductionDetailReport $production_detail_report)
    {
        $data = json_decode($request->getContent(), true) ?? $request->all();

        \Log::info('Store Payload:', $data);

        $validator = validator($data, [
            'salang_number' => 'required|integer|between:1,3',
            'production_date' => 'required|date',
            'time_per_salang' => 'required|string',
            'weight_per_salang' => 'nullable|string',
            'finished_weight_with_overflow' => 'nullable|string',
            'finished_weight_without_overflow' => 'nullable|string',
            'temperature' => 'required|string',
            'beginning_weight' => 'required_if:salang_number,1|string',
            'raw_material_weight_salang' => 'required_if:salang_number,3|string',
            'quantity_made' => 'nullable|string',
            'reject_quantity' => 'required_if:salang_number,3|string',
            'balance' => 'required_if:salang_number,3|string',
            'finished_product_weight' => 'required_if:salang_number,3|string',
            'ending_weight' => 'required_if:salang_number,3|string',
            'remarks' => 'nullable|string',
            'correct_size' => 'boolean',
            'correct_color' => 'boolean',
            'correct_hardness' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Set defaults for Salang 3 fields
        if ($validated['salang_number'] == 3) {
            $validated['ending_weight'] = $validated['ending_weight'] ?? '0';
            $validated['raw_material_weight_salang'] = $validated['raw_material_weight_salang'] ?? '0';
            $validated['finished_product_weight'] = $validated['finished_product_weight'];
            $validated['reject_quantity'] = $validated['reject_quantity'] ?? '0';
            $validated['balance'] = $validated['balance'] ?? '0';
        }

        // Prevent duplicate Salang number
        if ($production_detail_report->entries()->where('salang_number', $validated['salang_number'])->exists()) {
            return response()->json([
                'message' => "Salang {$validated['salang_number']} already exists for this report."
            ], 422);
        }

        $entry = $production_detail_report->entries()->create([
            'salang_number' => $validated['salang_number'],
            'production_date' => $validated['production_date'],
            'time_per_salang' => $validated['time_per_salang'],
            'weight_per_salang' => $validated['weight_per_salang'] ?? null,
            'finished_weight_with_overflow' => $validated['finished_weight_with_overflow'] ?? null,
            'finished_weight_without_overflow' => $validated['finished_weight_without_overflow'] ?? null,
            'temperature' => $validated['temperature'],
            'beginning_weight' => $validated['beginning_weight'] ?? null,
            'ending_weight' => $validated['ending_weight'] ?? null,
            'raw_material_weight_salang' => $validated['raw_material_weight_salang'] ?? null,
            'quantity_made' => $validated['quantity_made'] ?? null,
            'reject_quantity' => $validated['reject_quantity'] ?? null,
            'balance' => $validated['balance'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'correct_size' => $validated['correct_size'] ?? false,
            'correct_color' => $validated['correct_color'] ?? false,
            'correct_hardness' => $validated['correct_hardness'] ?? false,
            'finished_product_weight' => $validated['finished_product_weight'] ?? null,
            'pressman_id' => auth()->id(),
            'press' => $production_detail_report->press,
        ]);

        return response()->json([
            'message' => 'Entry created successfully',
            'entry' => $entry
        ], 201);
    }


    public function update(Request $request, ProductionDetailEntry $production_detail_entry)
{
    $data = json_decode($request->getContent(), true) ?? $request->all();

    \Log::info('Update Payload:', $data);

    $validator = validator($data, [
        'salang_number' => 'required|integer|between:1,3',
        'production_date' => 'required|date',
        'time_per_salang' => 'required|string',
        'weight_per_salang' => 'nullable|string',
        'finished_weight_with_overflow' => 'nullable|string',
        'finished_weight_without_overflow' => 'nullable|string',
        'temperature' => 'required|string',
        'beginning_weight' => 'required_if:salang_number,1|string',
        'ending_weight' => 'required_if:salang_number,3|string',
        'raw_material_weight_salang' => 'required_if:salang_number,3|string',
        'quantity_made' => 'required|string',
        'reject_quantity' => 'required_if:salang_number,3|string',
        'balance' => 'required_if:salang_number,3|string',
        'finished_product_weight' => 'required_if:salang_number,3|string',
        'remarks' => 'nullable|string',
        'correct_size' => 'boolean',
        'correct_color' => 'boolean',
        'correct_hardness' => 'boolean',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()
        ], 422);
    }

    $validated = $validator->validated();

    try {
        // Sanitize numeric fields to remove units or convert to numbers
        $numericFields = [
            'balance', 'quantity_made', 'finished_product_weight', 
            'ending_weight', 'raw_material_weight_salang', 
            'finished_weight_with_overflow', 'finished_weight_without_overflow', 
            'weight_per_salang', 'beginning_weight'
        ];

        foreach ($numericFields as $field) {
            if (isset($validated[$field])) {
                $validated[$field] = preg_replace('/[^\d.]/', '', $validated[$field]);
            }
        }

        $production_detail_entry->update([
            'salang_number' => $validated['salang_number'],
            'production_date' => $validated['production_date'],
            'time_per_salang' => $validated['time_per_salang'],
            'weight_per_salang' => $validated['weight_per_salang'] ?? null,
            'finished_weight_with_overflow' => $validated['finished_weight_with_overflow'] ?? null,
            'finished_weight_without_overflow' => $validated['finished_weight_without_overflow'] ?? null,
            'temperature' => $validated['temperature'],
            'beginning_weight' => $validated['beginning_weight'] ?? null,
            'ending_weight' => $validated['ending_weight'] ?? null,
            'raw_material_weight_salang' => $validated['raw_material_weight_salang'] ?? null,
            'quantity_made' => $validated['quantity_made'] ?? null,
            'reject_quantity' => $validated['reject_quantity'] ?? null,
            'balance' => $validated['balance'] ?? null,
            'finished_product_weight' => $validated['finished_product_weight'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'correct_size' => $validated['correct_size'] ?? false,
            'correct_color' => $validated['correct_color'] ?? false,
            'correct_hardness' => $validated['correct_hardness'] ?? false,
        ]);

        return response()->json([
            'message' => 'Entry updated successfully',
            'entry' => $production_detail_entry
        ], 200);

    } catch (\Exception $e) {
        \Log::error('Update Error: '.$e->getMessage(), ['payload' => $validated]);
        return response()->json([
            'message' => 'Failed to update entry: '.$e->getMessage()
        ], 500);
    }
}


    public function destroy(ProductionDetailEntry $production_detail_entry)
    {
        $production_detail_entry->delete();
        return response()->json(['success' => true]);
    }
}
