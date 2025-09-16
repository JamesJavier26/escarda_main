<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Item;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\Material;
use App\Models\Mould;
use App\Models\Unit;
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
            'activePressmen' => 1, // TODO: Replace with real logic if needed
            'totalEntries' => ProductionDetailEntry::count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return view('production_detail_reports.create', [
            'jo_no' => $request->jo_no,
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
            'ply' => $request->boolean('ply'),
            'metal_insert' => $request->boolean('metal_insert'),
            'is_linear' => $request->boolean('is_linear'),
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

            'remarks'             => 'nullable|string',
        ]);

        try {
            $report = ProductionDetailReport::create([
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
                'remarks'             => $validated['remarks'] ?? null,
            ]);

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
            'pressmanName' => $productionDetailReport->user->username ?? '',
            'plyTypes' => $productionDetailReport->ply_types,
            'metalInsertTypes' => $productionDetailReport->metal_insert_types,
            'selectedJoItem' => JobOrderItem::find($productionDetailReport->jo_item_id),
            'units' => Unit::select('id', 'name', 'short_name')->get(),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionDetailReport $production_detail_report)
    {
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

            'remarks'             => 'nullable|string',
        ]);

        try {
            $production_detail_report->update([
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
                'remarks'             => $validated['remarks'] ?? null,
            ]);

            return redirect()->back()->with('success', 'Production report updated successfully.');
        } catch (\Throwable $e) {
            Log::error('Update Failed: ' . $e->getMessage(), [
                'report_id' => $production_detail_report->id ?? null,
                'trace'     => $e->getTraceAsString(),
            ]);
            return redirect()->back()->withInput()->with('error', 'Unexpected error occurred. Please try again.');
        }
    }

    public function show(ProductionDetailReport $productionDetailReport) 
    {
        $productionDetailReport->load([
            'user', // Pressman
            'entries', // All production entries
            'jobOrder',
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
        return redirect()->back()->with('success', 'Production detail deleted successfully');
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

    public function fetchJoItem(ProductionDetailReport $report)
    {
        $joItem = JobOrderItem::with(['mould', 'jobOrder.salesOrder.customer'])
            ->find($report->jo_item_id);

        if (!$joItem) {
            return response()->json(['error' => 'JO Item not found'], 404);
        }

        // Determine mould details
        if ($joItem->mould_id) {
            $mouldDetails = 'Old: ' . ($joItem->mould->name ?? 'Unknown');
        } elseif ($joItem->new_mould_name) {
            $mouldDetails = 'New: ' . $joItem->new_mould_name;
        } else {
            $mouldDetails = null;
        }

        // Get customer from sales order
        $customerName = $joItem->jobOrder->salesOrder->customer->name ?? null;

        return response()->json([
            'id' => $joItem->id,
            'item_description' => $joItem->item_description,
            'material' => $joItem->material->name ?? null,
            'hardness' => $joItem->hardness,
            'thickness' => $joItem->thickness,
            'thickness_unit' => $joItem->thickness_unit, // <-- added
            'mould_details' => $mouldDetails,
            'jo_no' => $joItem->jobOrder->jo_no ?? null,
            'customer_name' => $customerName,
        ]);
    }

}
