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
        // Convert empty strings to null for all fields
        $input = array_map(function ($value) {
            return $value === "" ? null : $value;
        }, $request->all());

        $validated = validator($input, [
            'production_date' => 'required|date',
            'production_hour' => 'nullable|integer|min:0|max:23',
            'production_minute' => 'nullable|integer|min:0|max:59',
            'production_second' => 'nullable|integer|min:0|max:59',
            'temperature' => 'required|numeric|min:0',
            'beginning_weight' => 'required|numeric|min:0',
            'ending_weight' => 'required|numeric|min:0',
            'raw_material_weight_salang' => 'nullable|numeric|min:0',
            'finished_product_weight' => 'nullable|numeric|min:0',
            'quantity_made' => 'nullable|integer|min:0',
            'reject_quantity' => 'nullable|integer|min:0',
            'balance' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ])->validate();

        $entry = $production_detail_report->entries()->create([
            ...$validated,
            'production_detail_report_id' => $production_detail_report->id,
            'pressman_id' => auth()->id(),
            'press' => $production_detail_report->press,
        ]);

        return response()->json($entry, 201);
    }

    public function update(Request $request, ProductionDetailEntry $production_detail_entry)
    {
    //    dd($request->all());
        $request->merge(json_decode($request->getContent(), true));

        // Optional fallback if nothing is received
        if (empty($request->all())) {
            return response()->json(['message' => 'No data received'], 422);
        }

        // Validation
        $validated = $request->validate([
            'production_date' => 'required|date',
            'production_hour' => 'nullable|integer|min:0|max:23',
            'production_minute' => 'nullable|integer|min:0|max:59',
            'production_second' => 'nullable|integer|min:0|max:59',
            'temperature' => 'nullable|numeric',
            'beginning_weight' => 'required|numeric',
            'ending_weight' => 'required|numeric',
            'raw_material_weight_salang' => 'nullable|numeric',
            'finished_product_weight' => 'nullable|numeric',
            'quantity_made' => 'nullable|integer',
            'reject_quantity' => 'nullable|integer',
            'balance' => 'nullable|numeric',
            'remarks' => 'nullable|string',
        ]);
        // dd($production_detail_entry->id);
        $production_detail_entry->update($validated);

        return response()->json($production_detail_entry);
    }


    public function destroy(ProductionDetailEntry $production_detail_entry)
    {
        $production_detail_entry->delete();
        return response()->json(['success' => true]);
    }
}
