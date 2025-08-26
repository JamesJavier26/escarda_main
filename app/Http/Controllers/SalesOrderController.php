<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrderRequest;
use App\Models\Currency;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderComment;
use App\Models\User;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\PricingEstimate;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use function App\Helpers\notify_user;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        // Validate perPage input
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        // Base query with eager loads: customer, salesRep.employee, quotations, jobOrder with jo_items
        $query = SalesOrder::with([
            'customer',
            'salesRep.employee',
            'quotation',
            'approver',
            'jobOrder.jo_items',
            'currency',
        ]);
        
        // Role-based visibility
        if ($user->hasRole(['super-admin', 'admin'])) {
            $query->where(function ($q) use ($userId) {
                $q->where('so_status', '!=', 'draft')
                ->orWhere('sr_id', $userId); // allow own drafts
            });
        } elseif ($user->hasRole('operation-manager')) {
            $query->where('so_status', '!=', 'draft'); // ops manager sees all non-drafts only
        } else {
            $query->where('sr_id', $userId); // regular user: only their own
        }

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('so_no', 'like', "%{$search}%")
                ->orWhere('attention', 'like', "%{$search}%")
                ->orWhere('po_no', 'like', "%{$search}%")
                ->orWhere('pr_no', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('so_status', $status);
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Fetch paginated results
        $salesOrders = $query->latest('updated_at')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Counts for stat cards (global)
        $baseStatQuery = SalesOrder::query();

        if (!$user->hasRole(['super-admin', 'admin', 'operation-manager'])) {
            $baseStatQuery->where('sr_id', $userId);
        }

        return view('sales_orders.index', [
            'salesOrders'      => $salesOrders,
            'totalSalesOrders' => $baseStatQuery->count(),
            'pendingOrders'    => (clone $baseStatQuery)->where('so_status', 'pending')->count(),
            'approvedThisWeek' => (clone $baseStatQuery)->where('so_status', 'approved')
                                    ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                                    ->count(),
            'totalRevenue' => (clone $baseStatQuery)
                ->where('so_status', 'approved')
                ->with('jobOrder.jo_items')
                ->get()
                ->sum(function ($order) {
                    if (!$order->jobOrder || !$order->jobOrder->jo_items) return 0;

                    return $order->jobOrder->jo_items->sum(function ($item) {
                        return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
                    });
                }),
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
    $quotationId = $request->input('quotation_id');
    $quotation = null;
    $customer = null;

    if ($quotationId) {
        $quotation = Quotation::with('customer')->findOrFail($quotationId);
        $customer = $quotation->customer;
    }

        return view('sales_orders.create', [
            'quotation'         => $quotation,
            'initialCustomer'   => $customer,
            'editing'           => false,
            'so_no'             => SalesOrder::generateSONumber(),
            'currencies'        => Currency::all(),
            'salesReps'         => Employee::all(),
            'sales_order'       => new SalesOrder([
                'pr_no' => $quotation->pr_no ?? null,
                'po_no' => $request->po_no ?? null,
                'attention' => $quotation->attention ?? null,
                'delivery_date' => $request->input('delivery_date'),
                'remarks' => $quotation->remarks ?? null,
                'currency_id' => $quotation->currency_id ?? null,
                'tax_status' => $quotation->pricing_estimate->vat ?? null,
                'sr_id' => $quotation->sr_id ?? auth()->id(),
                'so_status' => 'pending',
            ])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(SalesOrderRequest $request)
{
    try {
        DB::beginTransaction();
        $current = (int) SalesOrder::max('so_no');
        $startAt = 6442;
        $so_no = $current ? $current + 1 : $startAt;

        $data = $request->validated();

        // Determine tax_status: use from request or fallback to customer's tax_status
        $taxStatus = $data['tax_status'] ?? null;
        if (empty($taxStatus) && !empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
            $taxStatus = $customer ? $customer->tax_status : null;
        }

        $order = SalesOrder::create([
            'date_ordered' => now(),
            'so_no' => $so_no,
            'pr_no' => $data['pr_no'],
            'po_no' => $data['po_no'],
            'attention' => $data['attention'],
            'customer_id' => $data['customer_id'],
            'delivery_date' => $data['delivery_date'],
            'sr_id' => $data['sr_id'],
            'remarks' => $data['remarks'],
            'currency_id' => $data['currency_id'],
            'tax_status' => $taxStatus,
            'terms' => $data['terms'] ?? null,
            'so_status' => 'draft',
        ]);

        // Create related job order
        $jobOrder = JobOrder::create([
            'jo_no' => $order->so_no, 
            'sales_order_id' => $order->id,
            'user_id' => auth()->id(),
            'jo_status' => 'draft',
            'approval_status' => 'draft',
        ]);

        // Save the jo_items (from new_items)
        foreach ($data['new_items'] as $item) {
            if (!empty($item['_delete'])) continue;

            JobOrderItem::create([
                'job_order_id' => $jobOrder->id,
                'sales_order_id' => $order->id,
                'item_id' => $item['item_id'],
                'item_name' => $item['name'],
                'item_description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['final_unit_price'] ?? $item['unit_price'],
                'unit_id' => $item['unit_id'],
            ]);
        }

        DB::commit();
        $this->saveNewUploads($order, $request);
        return redirect()->route('sales-orders.edit', $order)
            ->with('success', 'Sales Order created successfully.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to create Sales Order: ' . $e->getMessage());
    }
}


    /**
     * Display the specified resource.
     */
    public function show(SalesOrder $salesOrder)
    {
        return view('sales_orders.show', compact('salesOrder'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $salesOrder)
    {
        $salesOrder->load(['customer', 'salesRep.employee','pricing_estimate','currency']);

        return view('sales_orders.edit', [
            'so_no'             => $salesOrder->so_no,
            'sales_order'       => $salesOrder,
            'initialCustomer'   => $salesOrder->customer,
            'editing'           => true,
            'items'             => $salesOrder->items()->with(['item', 'unit'])->get(),
            'comments'          => $salesOrder->comments()
                                        ->with(['user:id,username'])
                                        ->orderBy('created_at', 'asc')
                                        ->get(),
            'currencies'        => Currency::all(),
        ]);
    }


    public function update(SalesOrderRequest $request, SalesOrder $salesOrder)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            $salesOrder->update([
                'pr_no'         => $data['pr_no'] ?? $salesOrder->pr_no,
                'po_no'         => $data['po_no'] ?? $salesOrder->po_no,
                'attention'     => $data['attention'] ?? $salesOrder->attention,
                'customer_id'   => $data['customer_id'] ?? $salesOrder->customer_id,
                'delivery_date' => $data['delivery_date'] ?? $salesOrder->delivery_date,
                'sr_id'         => $data['sr_id'] ?? $salesOrder->sr_id,
                'remarks'       => $data['remarks'] ?? $salesOrder->remarks,
                'currency_id'   => $data['currency_id'] ?? $salesOrder->currency_id,
                'tax_status'    => $data['tax_status'] ?? $salesOrder->tax_status,
                'so_status'     => 'draft',
            ]);

            $jobOrder = $salesOrder->jobOrder;

            // === UPDATE EXISTING ITEMS ===
            foreach ($request->input('items', []) as $id => $itemData) {
                $jobOrderItem = JobOrderItem::find($id);

                if (!$jobOrderItem) {
                    continue; // skip if item not found
                }

                // If flagged for delete
                if (!empty($itemData['_delete'])) {
                    $jobOrderItem->delete();
                    continue;
                }

                // Otherwise update
                $jobOrderItem->update([
                    'job_order_id'     => $jobOrder->id,
                    'sales_order_id'   => $salesOrder->id,
                    'item_id'          => $itemData['item_id'],
                    'item_name'        => $itemData['name'],
                    'item_description' => $itemData['description'],
                    'unit_id'          => $itemData['unit_id'],
                    'quantity'         => $itemData['quantity'],
                    'unit_price'       => $itemData['unit_price'],
                ]);
            }

            // === INSERT NEW ITEMS ===
            foreach ($request->input('new_items', []) as $itemData) {
                if (!empty($itemData['_delete'])) continue;

                JobOrderItem::create([
                    'job_order_id'     => $jobOrder->id,
                    'sales_order_id'   => $salesOrder->id,
                    'item_id'          => $itemData['item_id'],
                    'item_name'        => $itemData['name'],
                    'item_description' => $itemData['description'],
                    'unit_id'          => $itemData['unit_id'],
                    'quantity'         => $itemData['quantity'],
                    'unit_price'       => $itemData['unit_price'],
                ]);
            }

            DB::commit();

            // === ATTACHMENT HANDLING ===
            $keepIds = collect($request->input('keep_attachments', []));
            $salesOrder->attachments()
                ->whereNotIn('id', $keepIds)
                ->each(function ($attachment) {
                    Storage::disk($attachment->metadata['disk'] ?? 'public')
                        ->delete($attachment->metadata['path'] ?? '');
                    $attachment->delete();
                });

            foreach ($request->input('existing_file_labels', []) as $id => $label) {
                $attachment = $salesOrder->attachments()->find($id);
                if ($attachment) {
                    $attachment->update(['label' => $label]);
                }
            }

            $this->saveNewUploads($salesOrder, $request);

            return redirect()
                ->route('sales-orders.edit', $salesOrder)
                ->with('success', 'Sales Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update Sales Order: ' . $e->getMessage());
        }
    }




    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        //
    }

    public function storeComment(Request $request)
    {
        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
            'sales_order_id' => 'required|exists:sales_orders,id'
        ]);

        $comment = SalesOrderComment::create([
            'sales_order_id' => $validated['sales_order_id'],
            'comment' => $validated['comment'],
            'user_id' => auth()->id()
        ]);

        return response()->json($comment->load('user'));
    }

    public function submitForApproval(Request $request, SalesOrder $salesOrder)
    {
        if ($salesOrder->so_status !== 'draft') {
            return response()->json(['message' => 'Only draft estimates can be submitted.'], 422);
        }

        $salesOrder->update([
            'so_status' => 'pending',
        ]);

        $adminUsers = User::role(['admin', 'super-admin'])->get();

        foreach ($adminUsers as $user) {
            notify_user(
                $user->id,
                'Sales Order Submitted',
                "Sales Order #{$salesOrder->so_no} has been submitted for approval.",
                [
                    'action_button' => [
                        'label' => 'View SO',
                        'url' => route('sales-orders.show', $salesOrder->id),
                    ]
                ]
            );
        }

        return response()->json([
            'message' => 'Submitted for approval.',
            'redirect' => route('sales-orders.show', $salesOrder->id),
        ]);

    }

    public function reject($sales_order_id, Request $request)
    {
        $sales_order = SalesOrder::findOrFail($sales_order_id);

        $validated = $request->validate([
            'revision_reason' => 'nullable|string',
        ]);

        $sales_order->update([
            'so_status' => 'rejected',
        ]);
        notify_user(
            $sales_order->sr_id,
            'Sales Order Submitted',
            "Sales Order #{$sales_order->so_no} has been disapproved.",
            [
                'action_button' => [
                    'label' => 'View SO',
                    'url' => route('sales-orders.edit', $sales_order->id),
                ]
            ]
        );
        return back()->with('success', 'SO has been disapproved.');
    }

    private function saveNewUploads(SalesOrder $order, Request $request): void
    {
        if (!$request->hasFile('so_attachment')) return;

        $labels = $request->input('file_labels', []);

        foreach ($request->file('so_attachment') as $idx => $file) {
            $path = $file->store("sales-orders/{$order->id}", 'public');

            $order->attachments()->create([
                'label'    => $labels[$idx] ?? null,
                'metadata' => [
                    'disk'          => 'public',
                    'path'          => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type'     => $file->getMimeType(),
                    'size'          => $file->getSize(),
                ],
            ]);
        }
    }

}
