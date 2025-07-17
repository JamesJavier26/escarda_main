<?php

namespace App\Http\Controllers;

use App\Http\Requests\SalesOrderRequest;
use App\Models\Currency;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderComment;
use App\Models\User;
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

        // Base query
        $query = SalesOrder::with(['customer', 'salesRep.employee']);

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

        $so_no = $request->so_no;
        $sales_order = SalesOrder::with(['customer','salesRep','comments'])
                        ->where('so_no', $so_no)
                        ->first() ?? new SalesOrder(['so_no' => $so_no]);


        return view('sales_orders.create', [
            'so_no'         => null,
            'sales_order'   => $sales_order,
            'comments'      => [],
            'currencies' => Currency::all(),
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
                'is_vat' => $data['is_vat'],
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
                    'unit_price' => $item['unit_price'],
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
      
        if (in_array($salesOrder->so_status, ['pending', 'approved','canceled'])) {
                return redirect()->back()->with('error', 'You cannot edit a Sales Order that is pending, approved or canceled.');
            }

        return view('sales_orders.edit', [
            'so_no'         => $salesOrder->so_no,
            'sales_order'   => $salesOrder,
            'items'         => $salesOrder->items()->with(['item', 'unit'])->get(),
            'comments'      => $salesOrder->comments()
                ->with(['user:id,username']) 
                ->orderBy('created_at', 'asc')
                ->get(),
            'currencies' => Currency::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, SalesOrder $salesOrder)
    // {

    //     $validated = $request->validate([
    //         'po_no' => ['required', 'string'],
    //         'remarks' => ['required', 'string'],
    //     ]);

    //     $salesOrder->update($validated);

    //     return redirect()->route('sales-orders.edit', $salesOrder)
    //         ->with('success', 'Sales Order updated successfully');


    // }

    public function update(SalesOrderRequest $request, SalesOrder $salesOrder)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();

            // Update Sales Order fields
            $salesOrder->update([
                'pr_no'         => $data['pr_no'],
                'po_no'         => $data['po_no'],
                'attention'     => $data['attention'],
                'customer_id'   => $data['customer_id'],
                'delivery_date' => $data['delivery_date'],
                'sr_id'         => $data['sr_id'],
                'remarks'       => $data['remarks'],
                'currency_id'   => $data['currency_id'],
                'is_vat'        => $data['is_vat'],
                'so_status'     => 'draft',
            ]);

            $jobOrder = $salesOrder->jobOrder;

            // Handle existing items
            foreach ($request->input('items', []) as $id => $itemData) {
                if (!empty($itemData['_delete'])) {
                    JobOrderItem::where('id', $id)->delete();
                    continue;
                }

                JobOrderItem::updateOrCreate(
                    ['id' => $id],
                    [
                        'job_order_id'   => $jobOrder->id,
                        'sales_order_id' => $salesOrder->id,
                        'item_id'        => $itemData['item_id'],
                        'item_name'      => $itemData['name'],
                        'item_description'      => $itemData['description'],
                        'unit_id'        => $itemData['unit_id'],
                        'quantity'       => $itemData['quantity'],
                        'unit_price'     => $itemData['unit_price'],
                    ]
                );
            }

            // Handle new items
            foreach ($request->input('new_items', []) as $itemData) {
                if (!empty($itemData['_delete'])) continue;

                JobOrderItem::create([
                    'job_order_id'   => $jobOrder->id,
                    'sales_order_id' => $salesOrder->id,
                    'item_id'        => $itemData['item_id'],
                    'item_name'      => $itemData['name'],
                    'item_description'      => $itemData['description'],
                    'unit_id'        => $itemData['unit_id'],
                    'quantity'       => $itemData['quantity'],
                    'unit_price'     => $itemData['unit_price'],
                ]);
            }

            DB::commit();

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
            'so_status' => 'draft',
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
