<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Item;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\Material;
use App\Models\Mould;
use App\Models\ProductionDetailEntry;
use App\Models\ProductionDetailReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductionDetailReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productionReports = ProductionDetailReport::with(['jobOrder.customer', 'user'])
            ->withCount('entries')
            ->latest()
            ->paginate(10);

        return view('production_detail_reports.index', [
            'productionReports' => $productionReports,
            'totalReports' => ProductionDetailReport::count(),
            'todaysReports' => ProductionDetailReport::whereDate('created_at', today())->count(),
            'activePressmen' => 1,
            'totalEntries' => ProductionDetailEntry::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $jo_no = $request->jo_no;

        return view('production_detail_reports.create', [
            'jo_no' => $jo_no,
            'customers' => Customer::all(),
            'jobOrders' => JobOrder::latest()->get(),
            'productionDetailReports' => new ProductionDetailReport(),
            'materials' => Material::all(),
            'moulds' => Mould::all(),
            'employees' => Employee::all(),
            'items' => Item::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'ply' => $request->input('ply') == '1',
            'metal_insert' => $request->input('metal_insert') == '1',
            'is_linear' => filter_var($request->input('is_linear'), FILTER_VALIDATE_BOOLEAN),
        ]);

        $validated = $request->validate([
            'job_order_id' => 'required|exists:job_orders,id',
            'jo_no' => 'required|exists:job_orders,jo_no',
            'jo_item_id' => 'required|exists:job_order_items,id',
            'date_production' => 'required|date',
            'press' => 'required|string',

            'production_type' => 'required|in:old,new,trade',

            'ply' => 'boolean',
            'ply_types' => 'string|nullable',
            'ply_type_others' => 'nullable|string',

            'metal_insert' => 'boolean',
            'metal_insert_types' => 'string|nullable', // ✅ string only
            'metal_insert_details' => 'nullable|string',

            'is_linear' => 'boolean',

            'salang_1' => 'array|nullable',
            'salang_2' => 'array|nullable',
            'salang_3' => 'array|nullable',

            'remarks' => 'nullable|string',
        ]);

        try {
            $report = new ProductionDetailReport();

            $report->job_order_id = $validated['job_order_id'];
            $report->jo_no = $validated['jo_no'];
            $report->jo_item_id = $validated['jo_item_id'];
            $report->pressman_id = auth()->id();
            $report->press = $validated['press'];
            $report->date_production = $validated['date_production'];

            $report->production_type = $validated['production_type'];
            $report->is_linear = $validated['is_linear'] ?? false;

            // Ply
            $report->ply = $validated['ply'] ?? false;
            $report->ply_types = $validated['ply_types'] ?? null;
            $report->ply_type_others = $validated['ply_type_others'] ?? null;

            // Metal insert
            $report->metal_insert = $validated['metal_insert'] ?? false;
            $report->metal_insert_types = $validated['metal_insert_types'] ?? null; // ✅ string
            $report->metal_insert_details = $validated['metal_insert_details'] ?? null;

            // Salang entries
            $report->salang_1 = $request->input('salang_1', []);
            $report->salang_2 = $request->input('salang_2', []);
            $report->salang_3 = $request->input('salang_3', []);

            $report->remarks = $validated['remarks'] ?? null;

            $report->save();

            return redirect()->back()->with('success', 'Production report created successfully.');
        } catch (\Throwable $e) {
            Log::error('Store Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withInput()->with('error', 'Unexpected error occurred. Please try again.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionDetailReport $productionDetailReport)
    {
        $selectedJoItem = JobOrderItem::find($productionDetailReport->jo_item_id);

        return view('production_detail_reports.edit', [
            'editing' => true,
            'productionDetailReports' => $productionDetailReport,
            'jo_no' => $productionDetailReport->jo_no,
            'customers' => Customer::all(),
            'jobOrders' => JobOrder::latest()->get(),
            'materials' => Material::all(),
            'moulds' => Mould::all(),
            'employees' => Employee::all(),
            'items' => Item::all(),
            'plyTypes' => $productionDetailReport->ply_types,
            'metalInsertTypes' => $productionDetailReport->metal_insert_types, // ✅ string
            'selectedJoItem' => $selectedJoItem,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionDetailReport $production_detail_report)
    {
        $report = $production_detail_report;
        $request->merge([
            'ply'          => $request->boolean('ply'),
            'metal_insert' => $request->boolean('metal_insert'),
            'is_linear'    => $request->boolean('is_linear'),
        ]);

        $validated = $request->validate([
            'job_order_id'        => 'required|exists:job_orders,id',
            'jo_no'               => 'required|exists:job_orders,jo_no',
            'jo_item_id'          => 'required|exists:job_order_items,id',
            'date_production'     => 'required|date',
            'press'               => 'required|string',

            'production_type'     => 'required|in:old,new,trade',

            'ply'                 => 'boolean',
            'ply_types'           => 'nullable|string',
            'ply_type_others'     => 'nullable|string',

            'metal_insert'        => 'boolean',
            'metal_insert_types'  => 'nullable|string',
            'metal_insert_details'=> 'nullable|string',

            'is_linear'           => 'boolean',

            'salang_1'            => 'nullable|array',
            'salang_2'            => 'nullable|array',
            'salang_3'            => 'nullable|array',

            'remarks'             => 'nullable|string',
        ]);

        try {
            // Fill + Save instead of manually setting
            $report->fill([
            'job_order_id'        => $validated['job_order_id'],
            'jo_no'               => $validated['jo_no'],
            'jo_item_id'          => $validated['jo_item_id'],
            'pressman_id'         => auth()->id(),
            'press'               => $validated['press'],
            'date_production'     => $validated['date_production'],

            'production_type'     => $validated['production_type'],
            'is_linear'           => $validated['is_linear'] ?? false,

            'ply'                 => $validated['ply'] ?? false,
            'ply_types'           => $validated['ply_types'] ?? null,
            'ply_type_others'     => $validated['ply_type_others'] ?? null,

            'metal_insert'        => $validated['metal_insert'] ?? false,
            'metal_insert_types'  => $validated['metal_insert_types'] ?? null,
            'metal_insert_details'=> $validated['metal_insert_details'] ?? null,

            // Arrays
            'salang_1'            => $request->input('salang_1', []),
            'salang_2'            => $request->input('salang_2', []),
            'salang_3'            => $request->input('salang_3', []),

            'remarks'             => $validated['remarks'] ?? null,
        ]);

        $report->save();

            return redirect()
                ->back()
                ->with('success', 'Production report updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Update Failed: ' . $e->getMessage(), [
                'report_id' => $report->id ?? null,
                'trace'     => $e->getTraceAsString(),
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Unexpected error occurred. Please try again.');
        }
    }

    public function show(ProductionDetailReport $productionDetailReport) 
    {
        $productionDetailReport->load([
            'user', // Pressman
            'entries', // All production entries
            'jobOrder', // If jo_no comes from job_orders
        ]);

        return view('production_detail_reports.show', [
            'report' => $productionDetailReport,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductionDetailReport $productionDetailReport)
    {
        $productionDetailReport->delete();
        return redirect()->back()
            ->with('success', 'Production detail deleted successfully');
    }

    /**
     * Mark report as completed.
     */
    public function submit(ProductionDetailReport $report)
    {
        $report->update(['pdr_status' => 'completed']);

        return response()->json([
            'message' => 'Report marked as completed',
        ]);
    }
}
