<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Item;
use App\Models\JobOrder;
use App\Models\Material;
use App\Models\Mould;
use App\Models\ProductionDetailEntry;
use App\Models\ProductionDetailReport;
use App\Models\User;
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
        return view('production_detail_reports.index',  [
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
        // $job_order = JobOrder::where('jo_no', $jo_no)->first();
        // $productionDetailReports = ProductionDetailReport::where('jo_no', $jo_no)->get();

        return view('production_detail_reports.create', [
            'jo_no'         => $jo_no,
            'customers' => Customer::all(),
            'jobOrders' =>  JobOrder::latest()->get(),
            'productionDetailReports'     => new ProductionDetailReport(),
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
        try {
            $request->merge([
                'is_ready_to_use' => $request->has('is_ready_to_use'),
                'is_old_checked' => $request->has('is_old_checked'),
                'is_new_checked' => $request->has('is_new_checked'),
                'ply' => $request->input('ply') == 1,
                'metal_insert' => $request->input('metal_insert') == 1,
            ]);

            $data = $request->validate([
                'job_order_id' => 'required|exists:job_orders,id',
                'jo_no' => 'required|exists:job_orders,jo_no',
                'jo_item_id' => 'required|exists:job_order_items,id',
                'date_production' => 'required|date',
                'press' => 'required|string',
                'temperature' => 'required|string',
                'is_ready_to_use' => 'boolean',
                'ply' => 'boolean',
                'ply_types' => 'array|nullable',
                'ply_type_others' => 'required|string',
                'metal_insert' => 'boolean',
                'metal_insert_types' => 'array|nullable',
                'metal_insert_details' => 'nullable|string',
                'salang_1' => 'array|nullable',
                'salang_2' => 'array|nullable',
                'salang_3' => 'array|nullable',
                'remarks' => 'nullable|string',
            ]);

            $data['pressman_id'] = auth()->id();

            // Encode salangs as JSON
            $data['salang_1'] = $request->input('salang_1', []);
            $data['salang_2'] = $request->input('salang_2', []);
            $data['salang_3'] = $request->input('salang_3', []);

            Log::info('Creating Production Report', $data);

            ProductionDetailReport::create($data);

            return redirect()
                ->route('production-detail-reports.index')
                ->with('success', 'Production report created successfully.');

        } catch (\Exception $e) {
            Log::error('Failed to store Production Report: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'Failed to store report. Please try again.');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(ProductionDetailReport $productionDetailReport)
    {
        $productionDetailReport->load([
            'user',
            'entries',
            'jobOrder'
        ]);
        return view('production_detail_reports.show', [
                'report'  => $productionDetailReport,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductionDetailReport $productionDetailReport)
    {
        return view('production_detail_reports.edit', [
            'jo_no'         => $productionDetailReport->jo_no,
            'customers' => Customer::all(),
            'jobOrders' =>  JobOrder::latest()->get(),
            'productionDetailReports'     => $productionDetailReport,
            'materials' => Material::all(),
            'moulds' => Mould::all(),
            'employees' => Employee::all(),
            'items' => Item::all(),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductionDetailReport $report)
    {
        $request->merge([
            'is_ready_to_use' => $request->has('is_ready_to_use'),
            'is_old_checked' => $request->has('is_old_checked'),
            'is_new_checked' => $request->has('is_new_checked'),
            'ply' => $request->input('ply') == 1,
            'metal_insert' => $request->input('metal_insert') == 1,
        ]);

        $validated = $request->validate([
            'job_order_id' => 'required|exists:job_orders,id',
            'jo_no' => 'required|exists:job_orders,jo_no',
            'jo_item_id' => 'required|exists:job_order_items,id',
            'date_production' => 'required|date',
            'press' => 'required|string',
            'temperature' => 'required|string',
            'is_ready_to_use' => 'boolean',
            'ply' => 'boolean',
            'ply_types' => 'array|nullable',
            'ply_type_others' => 'required|string',
            'metal_insert' => 'boolean',
            'metal_insert_types' => 'array|nullable',
            'metal_insert_details' => 'nullable|string',
            'salang_1' => 'array|nullable',
            'salang_2' => 'array|nullable',
            'salang_3' => 'array|nullable',
            'remarks' => 'nullable|string',
        ]);

        try {
            $report->jo_no = $validated['jo_no'];
            $report->job_order_id = $validated['job_order_id'];
            $report->jo_item_id = $validated['jo_item_id'];
            $report->pressman_id = auth()->id();
            $report->press = $validated['press'];
            $report->temperature = $validated['temperature'];
            $report->date_production = $validated['date_production'];
            $report->is_old_checked = $validated['is_old_checked'] ?? false;
            $report->is_new_checked = $validated['is_new_checked'] ?? false;
            $report->is_ready_to_use = $validated['is_ready_to_use'] ?? false;
            $report->ply = $validated['ply'] ?? false;
            $report->ply_types = $validated['ply_types'] ?? [];
            $report->ply_type_others = $validated['ply_type_others'];
            $report->metal_insert = $validated['metal_insert'] ?? false;
            $report->metal_insert_types = $validated['metal_insert_types'] ?? [];
            $report->metal_insert_details = $validated['metal_insert_details'];
            $report->salang_1 = $validated['salang_1'] ?? [];
            $report->salang_2 = $validated['salang_2'] ?? [];
            $report->salang_3 = $validated['salang_3'] ?? [];
            $report->remarks = $validated['remarks'];

            $report->save();

            return redirect()->back()->with('success', 'Production report updated successfully.');
        } catch (\Throwable $e) {
            \Log::error('Update Failed: ' . $e->getMessage(), [
                'report_id' => $report->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()->with('error', 'Unexpected error occurred. Please try again.');
        }
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
    public function submit(ProductionDetailReport $report)
    {
        $report->update(['pdr_status' => 'completed']);

        return response()->json([
            'message' => 'Report marked as completed',
        ]);
    }
}
