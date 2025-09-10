<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\Material;
use App\Models\Mould;
use App\Models\Unit;
use App\Models\ProductionDetailReport;
use Illuminate\Http\Request;

class JobOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        $query = JobOrder::with(['salesOrder.customer', 'jo_items']);

        if ($user->hasRole(['super-admin', 'admin'])) {
            $query->where(function ($q) use ($userId) {
                $q->where('jo_status', '!=', 'draft')
                  ->orWhereHas('salesOrder', fn($so) => $so->where('sr_id', $userId));
            });
        } elseif ($user->hasRole('operation-manager')) {
            $query->where('jo_status', '!=', 'draft');
        } else {
            $query->whereHas('salesOrder', fn($so) => $so->where('sr_id', $userId));
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('jo_no', 'like', "%{$search}%")
                  ->orWhereHas('salesOrder.customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'to_deliver') {
                $query->where('jo_status', 'approved')
                    ->where('is_delivered', false);
            } else {
                $query->where('jo_status', $status);
            }
        }

        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($to)->endOfDay());
        }

        $jobOrders = $query->latest('updated_at')->paginate($perPage)->appends($request->except('page'));

        $baseStatQuery = JobOrder::query();

        if (!$user->hasRole(['super-admin', 'admin', 'operation-manager'])) {
            $baseStatQuery->whereHas('salesOrder', fn($so) => $so->where('sr_id', $userId));
        }

        return view('job_orders.index', [
            'jobOrders'          => $jobOrders,
            'totalJobOrders'     => $baseStatQuery->count(),
            'pendingJobOrders'   => (clone $baseStatQuery)->where('jo_status', 'pending')->count(),
            'approvedJobOrders'  => (clone $baseStatQuery)->where('jo_status', 'approved')->count(),
            'deliveredJobOrders' => (clone $baseStatQuery)->where('is_delivered', true)->count(),
            'cancelledJobOrders' => (clone $baseStatQuery)->where('jo_status', 'cancel')->count(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $jo_no = $request->jo_no;
        $job_order = JobOrder::where('jo_no', $jo_no)->first();

        return view('job_orders.create', [
            'jo_no'     => $jo_no,
            'job_order' => new JobOrder(),
            'materials' => Material::all(),
            'moulds'    => Mould::all(),
            'units'     => Unit::where('is_active', 1)->orderBy('name')->get(), // ✅ added
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        $jobOrder->load('jo_items.pricingEstimateItem');
        $validated = $request->validate([
            'job_order_id'   => 'required|exists:job_orders,id',
            'jo_no'          => 'required|string',
            'customer_id'    => 'required|exists:customers,id',
            'date_production'=> 'required|date',
            'temperature'    => 'nullable|string',
            'time'           => 'nullable|string',
            'press'          => 'nullable|string',
            'pressman'       => 'required|exists:employees,id',
            'day'            => 'nullable|string',
            'beginning'      => 'nullable|string',
            'ending'         => 'nullable|string',
            'raw_material'   => 'nullable|string',
            'actual_weight'  => 'nullable|string',
            'hours'          => 'nullable|string',
            'remarks'        => 'nullable|string',
            'items'          => 'required|array',
            'items.*.item_id'     => 'required|exists:items,id',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.made_date'   => 'nullable|date',
            'items.*.made_quantity'=> 'nullable|integer',
            'items.*.made_reject'  => 'nullable|integer',
        ]);

        $report = ProductionDetailReport::create([
            'job_order_id'   => $validated['job_order_id'],
            'jo_no'          => $validated['jo_no'],
            'customer_id'    => $validated['customer_id'],
            'date_production'=> $validated['date_production'],
            'temperature'    => $validated['temperature'],
            'time'           => $validated['time'],
            'press'          => $validated['press'],
            'pressman'       => $validated['pressman'],
            'day'            => $validated['day'],
            'beginning'      => $validated['beginning'],
            'ending'         => $validated['ending'],
            'raw_material'   => $validated['raw_material'],
            'actual_weight'  => $validated['actual_weight'],
            'hours'          => $validated['hours'],
            'remarks'        => $validated['remarks'],
        ]);

        foreach ($validated['items'] as $item) {
            $report->items()->create([
                'item_id'       => $item['item_id'],
                'material_id'   => $item['material_id'],
                'made_date'     => $item['made_date'],
                'made_quantity' => $item['made_quantity'],
                'made_reject'   => $item['made_reject'],
            ]);
        }

        return redirect()->route('job-orders.search', ['jo_no' => $validated['jo_no']])
            ->with('success', 'Production detail report saved successfully');
    }



    public function show(JobOrder $jobOrder)
    {
        return view('job_orders.show', compact('jobOrder'));
    }

    public function edit(JobOrder $jobOrder)
    {
        return view('job_orders.edit', [
            'jo_no'     => $jobOrder->jo_no,
            'job_order' => $jobOrder,
            'materials' => Material::all(),
            'moulds'    => Mould::all(),
            'units'     => Unit::where('is_active', 1)->orderBy('name')->get(), // ✅ added
        ]);
    }

    public function update(Request $request, JobOrder $jobOrder)
    {
        $jobOrder->load('jo_items.pricingEstimateItem');

        $validated = $request->validate([
            'duedate'            => ['nullable', 'date'],
            'remarks'            => ['nullable', 'string'],
            'delivery_details'   => ['nullable', 'string'],
            'instructions'       => ['nullable', 'string'],
            'mark_as_delivered'  => ['nullable', 'boolean'],
            'items'              => ['array'],
            'items.*.rubber_material' => ['nullable'],
            'items.*.hardness'        => ['nullable', 'string'],
            'items.*.thickness'       => ['nullable', 'string'],
            'items.*.thickness_unit'  => ['nullable', 'string'],
            'items.*.note'            => ['nullable', 'string', 'max:255'],

            // ✅ Fix: allow both old and new mould fields
            'items.*.mould_id'        => ['nullable', 'exists:moulds,id'],
            'items.*.new_mould_no'    => ['nullable', 'string', 'max:255'],
            'items.*.new_mould_name'  => ['nullable', 'string', 'max:255'],

            'items.*.is_linear'    => ['nullable', 'boolean'],
            'items.*.total_length' => ['nullable', 'string', 'required_if:items.*.is_linear,true'],
        ]);

        $jobOrder->update([
            'duedate'          => $validated['duedate'] ?? null,
            'remarks'          => $validated['remarks'] ?? null,
            'instructions'     => $validated['instructions'] ?? null,
            'delivery_details' => $validated['delivery_details'] ?? null,
            'status'           => $request->has('mark_as_delivered') ? 'delivered' : $jobOrder->status,
        ]);

        foreach ($validated['items'] as $index => $itemData) {
            $mouldId = $itemData['mould_id'] ?? null;

            $jobOrder->jo_items()
                ->where('id', $jobOrder->jo_items[$index]->id)
                ->update([
                    'material_id'     => $itemData['rubber_material'] ?? null,
                    'hardness'        => $itemData['hardness'] ?? '',
                    'thickness'       => $itemData['thickness'] ?? '',
                    'thickness_unit'  => $itemData['thickness_unit'] ?? '',
                    'mould_id'        => $mouldId,
                    'is_linear'       => $itemData['is_linear'] ?? false,
                    'total_length'    => $itemData['is_linear'] ? ($itemData['total_length'] ?? null) : null,

                    // ✅ Now these fields will persist
                    'new_mould_no'    => $itemData['new_mould_no'] ?? null,
                    'new_mould_name'  => $itemData['new_mould_name'] ?? null,
                    'note'            => $itemData['note'] ?? null,
                ]);
        }

        return redirect()->route('job-orders.edit', $jobOrder)
            ->with('success', 'Job Order updated successfully.');
    }


    public function destroy(JobOrder $jobOrder)
    {
        // Optional: handle hard deletes if required
    }

    public function activate(JobOrder $jobOrder)
    {
        try {
            if ($jobOrder->jo_status !== 'draft') {
                throw new \Exception('Only draft job orders can be activated');
            }

            $jobOrder->update([
                'approval_status' => 'pending',
                'jo_status' => 'pending',
                'approval_date' => null
            ]);

            if ($jobOrder->salesOrder) {
                $jobOrder->salesOrder->update([
                    'so_status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approval_date' => now(),
                ]);
            }

            return redirect()->route('job-orders.edit', $jobOrder)
                ->with('success', 'Job Order created!');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approve(JobOrder $jobOrder)
    {
        foreach ($jobOrder->jo_items as $item) {
            // Check if this item is for a new mould
            if (is_null($item->mould_id) && !empty($item->new_mould_no) && !empty($item->new_mould_name)) {
                
                // Create the new mould
                $mould = Mould::create([
                    'no'        => $item->new_mould_no,
                    'name'      => $item->new_mould_name,
                    'is_active' => true,
                ]);

                // Link the JO item to the new mould and clear temp fields
                $item->update([
                    'mould_id'       => $mould->id,
                    'new_mould_no'   => null,
                    'new_mould_name' => null,
                ]);
            }
        }

        // Approve the job order
        $jobOrder->update([
            'jo_status'        => 'approved',
            'approval_status'  => 'approved',
            'approval_date'    => now(),
            'approved_by'      => auth()->id(), // <-- optional: record approver
        ]);

        return redirect()
            ->route('job-orders.index')
            ->with('success', 'Job Order approved successfully.');
    }


    public function cancel(JobOrder $jobOrder)
    {
        $jobOrder->update([
            'jo_status' => 'cancel',
            'approval_status' => 'cancelled',
            'approval_date' => now(),
        ]);

        return redirect()->route('job-orders.index')->with('success', 'Job Order cancelled successfully.');
    }

    public function search1(Request $request)
    {
        $jo_no = $request->input('jo_no');

        $job_order = JobOrder::with([
                'salesOrder.customer',
                'jo_items.item',
                'jo_items.material',
                'jo_items.mould',
                'productionDetailReports.items',
                'productionDetailReports.pressmanEmployee'
            ])
            ->where('jo_no', $jo_no)
            ->first();

        if (!$job_order) {
            return redirect()->back()->with('error', 'Job Order not found');
        }

        $employees = Employee::all()->pluck('name', 'id');

        return view('your.view.name', [
            'job_order' => $job_order,
            'jo_no' => $jo_no,
            'materials' => Material::all(),
            'moulds' => Mould::all(),
            'employees' => $employees,
            'existing_reports' => $job_order->productionDetailReports
        ]);
    }

    public function markAsDelivered(JobOrder $job_order)
    {
        $job_order->update([
            'is_delivered' => true,
            'approval_date' => now(), // optional: keep or remove depending on meaning
        ]);

        return redirect()->route('job-orders.index')
            ->with('success', 'Job Order marked as delivered.');
    }

    public function search(Request $request)
    {
        $q = $request->get('q');

        return JobOrder::with(['customer'])
            ->where('jo_no', 'like', "%$q%")
            ->limit(10)
            ->get()
            ->map(function ($jo) {
                return [
                    'id' => $jo->id,
                    'jo_no' => $jo->jo_no,
                    'customer_name' => optional($jo->salesOrder->customer)->name ?? '--',
                    'so_no' => $jo->salesOrder->so_no,
                    'po_no' => $jo->salesOrder->po_no,
                    'pr_no' => $jo->salesOrder->pr_no,
                    'sales_rep' => optional($jo->salesOrder->salesRep->employee)->full_name ?? '--',
                    'delivery_details' => $jo->delivery_details ?? '--',
                    'remarks' => $jo->remarks,
                ];
            });
    }

    public function getItems(JobOrder $jobOrder)
    {
        return $jobOrder->jo_items()
            ->select('id', 'item_name', 'item_description','is_linear')
            ->get()
            ->map(function ($joItem) {
                return [
                    'id' => $joItem->id,
                    'item_name' => $joItem->item_name ?? 'Unnamed',
                    'item_desc' => $joItem->item_description ?? 'No description',
                    'is_linear' => (bool) ($joItem->is_linear ?? false),
                ];
            });
    }

    public function itemDetails($id)
    {
        try {
            $joItem = JobOrderItem::findOrFail($id);

            $productionType = null;
            if ($joItem->mould_id) $productionType = 'old';
            elseif ($joItem->new_mould_no) $productionType = 'new';
            elseif ($joItem->note) $productionType = 'trade';

            return response()->json([
                'is_linear' => (bool) $joItem->is_linear,
                'production_type' => $productionType,
            ]);
        } catch (\Throwable $e) {
            \Log::error('JO item details error: '.$e->getMessage(), ['id' => $id]);
            return response()->json(['error' => 'Failed to fetch JO item details'], 500);
        }
    }
}
