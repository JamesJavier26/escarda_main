<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Models\Item;
use App\Models\JobOrder;
use App\Models\JobOrderItem;
use App\Models\PricingEstimate;
use App\Models\PricingEstimateItem;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Setting;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use function App\Helpers\notify_user;
use Barryvdh\DomPDF\PDF;

class QuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {
        $user = auth()->user();
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        $query = Quotation::with(['pricing_estimate.customer', 'salesOrder']);

        // If user is NOT super-admin or admin, limit quotations to only those created by the user
        if (! $user->hasAnyRole(['super-admin', 'admin'])) {
            $query->where('created_by', $user->id);
        }

        // Search logic
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quote_no', 'like', "%$search%")
                ->orWhereHas('pricing_estimate', fn ($q) =>
                        $q->where('pe_no', 'like', "%$search%")
                        ->orWhere('attention', 'like', "%$search%")
                        ->orWhereHas('customer', fn ($c) =>
                            $c->where('name', 'like', "%$search%")
                        )
                );
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('quote_status', $status);
        }

        // Filter by date range
        if ($from = $request->input('from')) {
            $query->where('quote_date', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->input('to')) {
            $query->where('quote_date', '<=', Carbon::parse($to)->endOfDay());
        }

        // Final paginated results
        $quotations = $query->latest('updated_at')
                            ->paginate($perPage)
                            ->appends($request->except('page'));

        return view('quotations.index', [
            'quotations'         => $quotations,
            'totalQuotations'    => Quotation::count(),
            'approvedQuotation'  => Quotation::where('quote_status', 'approved')->count(),
            'pendingQuotation'   => Quotation::where('quote_status', 'pending')->count(),
            'rejectQuotation'    => Quotation::where('quote_status', 'rejected')->count(),
        ]);
    }


    public function quote_items($pe_id)
    {
        $current = (int) Quotation::max('quote_no');
        $startAt = 6442;
        $quote_no = $current ? $current + 1 : $startAt;
        $pe = PricingEstimate::find($pe_id);
        $pe_items = PricingEstimateItem::where('pricing_estimate_id',$pe_id)->paginate(10);
        // dd($items);
        return view('quotations.items', compact('pe','pe_items','quote_no'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $pe_no = $request->pe_no;
        $pricing_estimate = PricingEstimate::with(['customer','salesRep','pe_items'])->first();

        $current = (int) Quotation::max('quote_no');
        $startAt = 6442;
        $quote_no = $current ? $current + 1 : $startAt;

        return view('quotations.create',[
            // 'quotation' => new Quotation,
            'quotation' => Quotation::where('pricing_estimate_id', $pricing_estimate->id)->first(),
            'quote_no'  => $quote_no,
            'pe_no' => $pe_no,
            'pricing_estimate' => $pricing_estimate,
            'pe_items' => PricingEstimateItem::where('pricing_estimate_id',$pricing_estimate->id)->paginate(10) 
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Quotation $quotation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $quotation)
    {
        // $quotation = Quotation::where('pricing_estimate_id', $quotation->pricing_estimate_id)->first();
       
        return view('quotations.edit',[
            // 'quotation' => new Quotation,
            'quotation' => $quotation,
            'quote_no'  => $quotation->quote_no,
            'pe_no' => $quotation->pricing_estimate->pe_no,
            'pricing_estimate' => $quotation->pricing_estimate,
            'pe_items' => PricingEstimateItem::where('pricing_estimate_id',$quotation->pricing_estimate_id)->paginate(10) 
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $quotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $quotation)
    {
        //
    }

    public function approve(Quotation $quotation)
    {
        // Validate that the quotation is in pending status
        if ($quotation->quote_status !== 'pending') {
            return back()->with('error', 'Only pending quotations can be approved.');
        }

        $quotation->update([
            'quote_status' => 'approved',
            'is_final' => true,
            'quote_date' => now(),
        ]);
        if ($quotation->pricing_estimate) {
            $quotation->pricing_estimate->update([
                'pe_status' => 'approved',
            ]);
        }
        notify_user(
            $quotation->pricing_estimate->sr_id, 
            'Quotation Approved',
            "Quotation #{$quotation->quote_no} has been approved.",
            [
                'action_button' => [
                    'label' => 'View Quotation',
                    'url' => route('quotations.edit', $quotation->id),
                ]
            ]
        );
        // return redirect()->route('quotations.edit', $quotation)->with('success', 'Quotation has been approved.');
        return redirect()->back()->with('success', 'Quotation has been approved.');
    }

    public function reject(Quotation $quotation, Request $request,PDF $pdf)
    {
        if ($quotation->quote_status !== 'pending') {
            return response()->json(['message' => 'Only pending quotations can be rejected.'], 422);
        }

        $validated = $request->validate([
            'revision_reason' => 'nullable|string',
        ]);

        $quotation->update([
            'quote_status' => 'rejected', // ← FIX: This should be 'rejected', not 'pending'
            'revision_reason' => $validated['revision_reason'],
            'rejected_at' => now(),
        ]);

        if ($quotation->pricing_estimate) {
            $quotation->pricing_estimate->update([
                'pe_status' => 'pending',
                'updated_at' => now(),
            ]);
        }
        $data = [
            'pe_no' => $quotation->pricing_estimate->pe_no,
            'price_estimates' => $quotation->pricing_estimate,
            'date' => now()->format('F d, Y'),
            'setting' => Setting::all(),
            'revision_reason' => $validated['revision_reason'] ?? null,
            'rejected_at'   => now(),
            'is_rejected' => true,
        ];

        // Generate PDF
        $pdf->loadView('reports.generateQuote', $data)
                    ->setPaper('a4', 'portrait');

        // Ensure directory exists
        $directory = storage_path('app/public/quotations/rejected');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save PDF to storage
        $filename = "quotation_{$quotation->quote_no}_rejected.pdf";
        $pdfPath = storage_path("app/public/quotations/rejected/{$filename}");
        $pdf->save($pdfPath);

        $quotation->update([
            'rejected_pdf_path' => "quotations/rejected/{$filename}"
        ]);

        notify_user(
            $quotation->pricing_estimate->sr_id,
            'Quotation Disapproved',
            "Quotation #{$quotation->quote_no} has been disapproved. {$quotation->revision_reason}",
            [
                'attachment' => $pdfPath,
                'action_button' => [
                    'label' => 'Revise Pricing',
                    'url' => route('pricing-estimates.price-entry', $quotation->pricing_estimate->id),
                ]
            ]
        );

        return response()->json(['message' => 'Quotation has been disapproved.']);
    }

    // public function exportToSalesOrder1(Quotation $quotation)
    // {
    //     try {
    //         DB::beginTransaction();

    //         // Validate the quotation is approved
    //         if ($quotation->quote_status !== 'approved') {
    //             throw new \Exception('Only approved quotations can be exported to sales orders');
    //         }

    //         $current = (int) SalesOrder::max('so_no');
    //         $startAt = 6442;
    //         $so_no = $current ? $current + 1 : $startAt;
    //         // Generate SO and JO numbers (same number for both)
    //         $soNo = $so_no;
    //         $joNo = $so_no;

    //         // Create Sales Order
    //         $salesOrder = SalesOrder::create([
    //             'so_no' => $soNo,
    //             'date_ordered' => now(),
    //             'customer_id' => $quotation->pricing_estimate->customer_id,
    //             'pr_no' => $quotation->pricing_estimate->pr_no,
    //             'attention' => $quotation->pricing_estimate->customer->telno,
    //             'po_no' => '', 
    //             'sr_id' => auth()->id(),
    //             'jo_no' => $joNo, 
    //             'is_vat' => true,
    //             'approval_status' => 'draft',
    //             'quotation_id' => $quotation->id,
    //             'currency_id' => $quotation->currency_id,
    //             'is_notified' => false,
    //         ]);

    //         // Create Job Order
    //         $jobOrder = JobOrder::create([
    //             'jo_no' => $joNo,
    //             'sales_order_id' => $salesOrder->id,
    //             'user_id' => auth()->id(),
    //             'jo_status' => 'draft',
    //             'approval_status' => 'draft',
    //             'approval_date' => null,
    //         ]);

    //         // Copy items from Price Estimate to Job Order Items
    //         foreach ($quotation->pricing_estimate->pe_items as $peItem) {
    //             JobOrderItem::create([
    //                 'job_order_id' => $jobOrder->id,
    //                 'sales_order_id' => $salesOrder->id,
    //                 'item_id' => $peItem->item_id,
    //                 'mould_id' => $peItem->mould_id ?? null,
    //                 'material_id' => $peItem->material_id ?? null,
    //                 'hardness' => $peItem->hardness ?? null,
    //                 'thickness' => $peItem->thickness ?? null,
    //                 'quantity' => $peItem->quantity,
    //                 'unit_id' => $peItem->unit_id,
    //                 'unit_price' => $peItem->unit_price,
    //             ]);
    //         }

    //         DB::commit();

    //         return redirect()->route('sales-orders.edit', $salesOrder)
    //             ->with('success', 'Sales Order created successfully');

    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return back()->with('error', 'Failed to create sales order: ' . $e->getMessage());
    //     }
    // }


    public function exportToSalesOrder(Quotation $quotation, Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            if ($quotation->quote_status !== 'approved') {
                throw new \Exception('Only approved quotations can be exported to sales orders');
            }

            $selectedIds = json_decode($request->input('selected_items'), true);

            if (empty($selectedIds) || !is_array($selectedIds)) {
                throw new \Exception('No items selected for export.');
            }
            
            $alreadyExported = JobOrderItem::whereIn('item_id', $selectedIds)->pluck('item_id')->toArray();

            if (!empty($alreadyExported)) {
                // throw new \Exception('One or more selected items have already been exported to a Sales Order.');
                return back()->with('error', 'One or more selected items have already been exported to a Sales Order.');
            }

            $current = (int) SalesOrder::max('so_no');
            $startAt = 6442;
            $so_no = $current ? $current + 1 : $startAt;
            $soNo = $so_no;
            $joNo = $so_no;

            $salesOrder = SalesOrder::create([
                'so_no' => $soNo,
                'date_ordered' => now(),
                'customer_id' => $quotation->pricing_estimate->customer_id,
                'pr_no' => $quotation->pricing_estimate->pr_no,
                'attention' => $quotation->pricing_estimate->attention,
                'terms' => $quotation->pricing_estimate->terms,
                'delivery_date' => $quotation->pricing_estimate->delivery_date,
                'po_no' => '', 
                'sr_id' => auth()->id(),
                'jo_no' => $joNo,
                'tax_status' => $quotation->pricing_estimate->customer->tax_status,
                'approval_status' => 'draft',
                'so_status' => 'draft',
                'quotation_id' => $quotation->id,
                'currency_id' => $quotation->currency_id,
                'is_notified' => false,
            ]);

            $jobOrder = JobOrder::create([
                'jo_no' => $joNo,
                'sales_order_id' => $salesOrder->id,
                'user_id' => auth()->id(),
                'jo_status' => 'draft',
                'approval_status' => 'draft',
                'approval_date' => null,
            ]);

            // Only export selected pe_items
            $selectedItems = $quotation->pricing_estimate->pe_items()->whereIn('id', $selectedIds)->get();

            foreach ($selectedItems as $peItem) {
                $item = Item::where('name', $peItem->item_name)->first();

                if (!$item) {
            
                    $item = Item::create([
                        'number' => Helper::generateNextItemNumber(),
                        'name' => $peItem->item_name,
                        'description' => $peItem->item_description,
                        'unit_id' => $peItem->unit_id,
                        'category_id' => null,
                        'remarks' => null,
                        'is_taxable' => true,
                        'is_active' => true,
                        'created_by' => auth()->id(),
                    ]);
                }


                JobOrderItem::create([
                    'job_order_id' => $jobOrder->id,
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $item->id,
                    'item_name' => $peItem->item_name,
                    'item_description' => $peItem->item_description,
                    'mould_id' => $peItem->mould_id ?? null,
                    'material_id' => $peItem->material_id ?? null,
                    'hardness' => $peItem->hardness ?? null,
                    'thickness' => $peItem->thickness ?? null,
                    'quantity' => $peItem->quantity,
                    'unit_id' => $peItem->unit_id,
                    'unit_price' => $peItem->final_unit_price ?? $peItem->unit_price,
                ]);
            }

            DB::commit();

            return redirect()->route('sales-orders.edit', $salesOrder)
                ->with('success', 'Sales Order created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create sales order: ' . $e->getMessage());
        }
    }


    public function generate(Request $request, PricingEstimate $pricing_estimate)
    {
        // dd($request->quote_remarks);
        // Check if a quotation already exists
        $quotation = Quotation::where('pricing_estimate_id', $pricing_estimate->id)
                            ->latest('created_at')
                            ->first();

        // If no quotation exists, create the first one
        if (!$quotation) {
            // $quoteNo = Quotation::max('quote_no') ?? 6442;
            // $newQuoteNo = $quoteNo + 1;
            $baseQuotes = Quotation::whereRaw("quote_no NOT LIKE '%_Rev%'")->pluck('quote_no');
            $maxQuote = $baseQuotes->map(fn ($q) => (int) $q)->max() ?? 6442;
            $newQuoteNo = $maxQuote + 1;


            $newQuotation = Quotation::create([
                'quote_no' => $newQuoteNo,
                'pricing_estimate_id' => $pricing_estimate->id,
                'quote_date' => now(),
                'quote_status' => 'pending',
                'quote_remarks' => $request->remarks,
                'currency_id' => $pricing_estimate->currency_id,
            ]);

            

            return redirect()->route('quotations.edit', $newQuotation)->with('success', 'Quotation created successfully!');
        }

        // If existing quotation is pending or approved, just return it
        if (in_array($quotation->quote_status, ['pending', 'approved'])) {
            return redirect()->route('quotations.edit', $quotation)->with('info', 'Existing quotation loaded.');
        }

        // If existing is rejected, create revision
        if ($quotation->quote_status === 'rejected') {
            // Extract base quote number
            $baseNo = explode('_', $quotation->quote_no)[0];

            // Count existing revisions
            $revCount = Quotation::where('quote_no', 'LIKE', "{$baseNo}_Rev%")->count();
            $newQuoteNo = "{$baseNo}_Rev" . ($revCount + 1);

            $newQuotation = Quotation::create([
                'quote_no' => $newQuoteNo,
                'pricing_estimate_id' => $pricing_estimate->id,
                'quote_date' => now(),
                'quote_status' => 'pending',
                'currency_id' => $pricing_estimate->currency_id,
                'parent_id' => $quotation->id,
                'quote_remarks' => $request->remarks,
            ]);

            // Optionally copy items from old quotation (if needed)
            foreach ($quotation->quotation_items as $item) {
                $newQuotation->quotation_items()->create($item->toArray());
            }
            $quotation->pricing_estimate->update([
                'pe_status' => 'approved',
            ]);


            return redirect()->route('quotations.edit', $newQuotation)->with('success', 'Revision quotation created.');
        }

        // Fallback
        return redirect()->route('quotations.index')->with('error', 'Unable to generate quotation.');
    }

}
