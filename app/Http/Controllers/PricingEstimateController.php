<?php

namespace App\Http\Controllers;

use App\Http\Requests\PricingEstimateRequest;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\ItemCategory;
use App\Models\PricingEstimate;
use App\Models\PricingEstimateItem;
use App\Models\ProductionDetailReport;
use App\Models\Quotation;
use App\Models\SalesOrder;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use function App\Helpers\notify_user;

class PricingEstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        // Validate perPage input with fallback
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100]) 
            ? (int) $request->input('perPage') 
            : 10;

        // Base query: exclude drafts, eager load relationships, count related
        $query = PricingEstimate::with(['customer', 'salesRep.employee'])
            ->withCount(['pe_items', 'quotations'])
            ->where('pe_status', '!=', 'draft');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pe_no', 'like', "%$search%")
                ->orWhere('attention', 'like', "%$search%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$search%"));
            });
        }

        // Filter by status (optional)
        if ($status = $request->input('status')) {
            $query->where('pe_status', $status);
        }

        // Date range filter
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }

        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Paginate with query string to retain filters
        $pricingEstimates = $query->latest('updated_at')->paginate($perPage)->appends($request->except('page'));

        return view('pricing_estimates.index', [
            'pricingEstimates'    => $pricingEstimates,
            'totalEstimates'      => PricingEstimate::where('pe_status', '!=', 'draft')->count(),
            'draftEstimates'      => PricingEstimate::where('pe_status', 'draft')->count(),
            'pendingEstimates'    => PricingEstimate::where('pe_status', 'pending')->count(),
            'totalRevenue'        => 0, // You can compute this if needed
        ]);
    }

    public function my_estimates(Request $request)
    {
        $user = auth()->user();
        // dd($user->id);
        
        // Validate perPage input with fallback
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100]) 
            ? (int) $request->input('perPage') 
            : 10;

        $query = PricingEstimate::with(['customer', 'salesRep.employee'])
            ->withCount(['pe_items', 'quotations'])
            ->where(function($q) use ($user) {
                $q->where(function($q1) use ($user) {
                        // All assigned to sales rep, but exclude drafts
                        $q1->where('sr_id', $user->id)
                        ->where('pe_status', '!=', 'draft');
                    })
                ->orWhere(function($q2) use ($user) {
                        // Only drafts created by the current user
                        $q2->where('created_by', $user->id)
                        ->where('pe_status', 'draft');
                    });
            });

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pe_no', 'like', "%$search%")
                    ->orWhere('attention', 'like', "%$search%")
                    ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%$search%"));
            });
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('pe_status', $status);
        }

        // Date range filter
        if ($request->filled('from') && $request->filled('to')) {
            $query->whereBetween('created_at', [
                Carbon::parse($request->input('from'))->startOfDay(),
                Carbon::parse($request->input('to'))->endOfDay()
            ]);
        }

        // Paginate with query string to retain filters
        $pricingEstimates = $query->latest('updated_at')->paginate($perPage)->appends($request->except('page'));
        // dd($pricingEstimates);
        return view('pricing_estimates.my_estimates', [
            'pricingEstimates'    => $pricingEstimates,
            'totalEstimates'      => PricingEstimate::where('sr_id', $user->id)->count(),
            'draftEstimates'      => PricingEstimate::where('pe_status', 'draft')->where('sr_id', $user->id)->count(),
            'pendingEstimates'    => PricingEstimate::where('pe_status', 'pending')->where('sr_id', $user->id)->count(),
            'totalRevenue'        => 0,
        ]);
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::select('id', 'name', 'address', 'email', 'telno', 'tin_no', 'website', 'tax_status')->get();

        $current = (int) PricingEstimate::max('pe_no');
        $startAt = 2015;
        $pe_no = $current ? $current + 1 : $startAt;

        $currencies = Currency::where('is_active', true)->get();

        return view('pricing_estimates.create', [
            'pricing_estimate' => new PricingEstimate,
            'currencies' => $currencies,
            'pe_no' => $pe_no,
            'categories' => ItemCategory::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
            'users' => User::with('employee')->orderBy('username')->get(),
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */

    // public function store(Request $request)
    // {
    //     dd($request->all());
    // }
    public function store(PricingEstimateRequest $request)
    {
        // Validate request data
        $data = $request->validated();

        // Attach the authenticated user's ID
        $data['created_by'] = auth()->id();

        // Create the pricing estimate
        $estimate = PricingEstimate::create($data);

        // Loop through items
        foreach ($request->input('new_items', []) as $itemData) {
            if (!empty($itemData['_delete'])) continue;

            \Log::debug('Creating item for estimate ID: ' . $estimate->id, $itemData);

            PricingEstimateItem::create([
                'pricing_estimate_id' => $estimate->id,
                'item_id' => $itemData['item_id'],
                'item_name' => $itemData['name'],
                'item_description' => $itemData['description'],
                'pe_remarks' => $itemData['pe_remarks'],
                'unit_id' => $itemData['unit_id'] ?? null,
                'notes' => $itemData['notes'],
                'internal_notes' => $itemData['internal_notes'] ?? null,
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
            ]);
        }

        // Handle file uploads
        $this->saveNewUploads($estimate, $request);

        // Log the user who created this estimate
        \Log::info('Pricing Estimate ID ' . $estimate->id . ' created by user ID ' . auth()->id());

        return redirect()
            ->route('pricing-estimates.create')
            ->with('success', 'Pricing Estimate saved successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(PricingEstimate $pricingEstimate)
    {
        $pricingEstimate->load('salesRep.employee');
        return view('pricing_estimates.show', ['pricingEstimate'=> $pricingEstimate]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PricingEstimate $pricingEstimate)
    {
        if (in_array($pricingEstimate->pe_status, ['pending', 'approved','canceled'])) {
            return redirect()->back()->with('error', 'You cannot edit a Pricing Estimate that is pending, approved or canceled.');
        }

        $pricingEstimate->load('customer', 'salesRep.employee');

        return view('pricing_estimates.edit', [
            'pricing_estimate' => $pricingEstimate,
            'currencies'       => Currency::where('is_active', true)->get(),
            'pe_no'            => $pricingEstimate->pe_no,
            'categories'       => ItemCategory::orderBy('name')->get(),
            'units'            => Unit::orderBy('name')->get(),
            'users'            => User::with('employee')->orderBy('username')->get(),
            'initialCustomer'  => json_encode($pricingEstimate->customer), // ✅ Final change
            'editing'          => true,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(PricingEstimateRequest $request, PricingEstimate $pricingEstimate)
    {
        $data  = $request->validated();
        $pricingEstimate->update($data);

        // Handle removed offers (from Alpine)
        $removedOffers = $request->input('removed_offers', '');
        if ($removedOffers) {
            $ids = explode(',', $removedOffers);
            PricingEstimateItem::whereIn('id', $ids)->delete();
        }
        // Handle existing items
        foreach ($request->input('items', []) as $id => $data) {
            if (!empty($data['_delete'])) {
                PricingEstimateItem::where('id', $id)->delete();
                continue;
            }

            PricingEstimateItem::updateOrCreate(
                ['id' => $id],
                [
                    'pricing_estimate_id' => $pricingEstimate->id,
                    'item_id'             => $data['item_id'],
                    'item_name'           => $data['name'],
                    'item_description'           => $data['description'],
                    'pe_remarks'          => $data['pe_remarks'] ?? '',
                    'unit_id'             => $data['unit_id'] ?? null,
                    'notes'               => $data['notes'] ?? '',
                    'internal_notes'      => $data['internal_notes'] ?? '',
                    'quantity'            => $data['quantity'],
                    'unit_price'          => $data['unit_price'],
                ]
            );
        }


        foreach ($request->input('new_items', []) as $data) {
            if (!empty($data['_delete'])) continue;

            PricingEstimateItem::create([
                'pricing_estimate_id' => $pricingEstimate->id,
                'item_id'             => $data['item_id'],
                'item_name'           => $data['name'],
                'item_description'    => $data['description'],
                'pe_remarks'          => $data['pe_remarks'] ?? '',
                'unit_id'             => $data['unit_id'] ?? null,
                'notes'               => $data['notes'] ?? '',
                'internal_notes'      => $data['internal_notes'] ?? '',
                'quantity'            => $data['quantity'],
                'unit_price'          => $data['unit_price'],
            ]);
        }
        $keepIds = collect($request->input('keep_attachments', []));
        $pricingEstimate->attachments()
            ->whereNotIn('id', $keepIds)
            ->each(function ($attachment) {
                Storage::disk($attachment->metadata['disk'] ?? 'public')
                    ->delete($attachment->metadata['path'] ?? '');
                $attachment->delete();
            });

        foreach ($request->input('existing_file_labels', []) as $id => $label) {
            $attachment = $pricingEstimate->attachments()->find($id);
            if ($attachment) {
                $attachment->update(['label' => $label]);
            }
        }

        $this->saveNewUploads($pricingEstimate, $request);

        return redirect()
            ->route('pricing-estimates.edit', $pricingEstimate)
            ->with('success', 'Pricing Estimate updated successfully.');
    }


    public function update1(PricingEstimateRequest $request, PricingEstimate $pricing_estimate)
    {
        $data  = $request->validated();
        $items = collect($data['items'] ?? []); unset($data['items']);

        DB::transaction(function () use ($data, $items, $request, $pricing_estimate) {
            $pricing_estimate->update($data);

            foreach ($request->input('existing_file_labels', []) as $id => $lbl) {
                $pricing_estimate->attachments()->whereKey($id)->update(['label'=>$lbl]);
            }

            $keep = $request->input('keep_attachments', []);
            $pricing_estimate->attachments()
                ->whereNotIn('id', $keep)
                ->get()
                ->each(function ($file) {
                    Storage::disk($file->metadata['disk'])->delete($file->metadata['path']);
                    $file->delete();
                });

      
            $this->saveNewUploads($pricing_estimate, $request);
        });

        return $request->wantsJson()
            ? response()->json(['message'=>'Updated','redirect'=>route('pricing-estimates.edit',$pricing_estimate)])
            : back()->with('success','Updated');
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PricingEstimate $pricingEstimate)
    {
        $pricingEstimate->delete();

        return redirect()
            ->route('pricing-estimates.index')
            ->with('success', 'Pricing estimate deleted successfully.');
    }

      public function destroyItem(PricingEstimateItem $item)
    {
         $item->delete();

        return response()->json([
            'success' => true,
            'id' => $item->id,
            'message' => 'Offer item deleted successfully.'
        ]);
    }
    

    public function generateQuote(PDF $pdf, Request $request)   // ← Laravel will inject it
    {
        $pe_no = $request->input('pe_no');
        $price_estimates = PricingEstimate::with(['customer','salesRep','pe_items','currency'])->where('pe_no',$pe_no)->first();
        //  dd($price_estimates);
        $data = [
            'pe_no'        => $pe_no,
            'price_estimates' => $price_estimates,
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),

        ];

        $pdf->loadView('reports.generateQuote', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('PE.pdf');
    }

    public function generateSales(PDF $pdf, Request $request)   // ← Laravel will inject it
    {
        $so_no = $request->input('so_no');
        $salesOrder = SalesOrder::with(['customer', 'salesRep.employee'])
        ->where('so_no',$so_no)
        ->latest()
        ->first();

        $data = [
            'so_no'        => $so_no,
            'salesOrder' => $salesOrder,
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
            'price_estimates' => PricingEstimate::with(['customer','salesRep','pe_items','currency'])->where('id',1)->first(),

        ];

        $pdf->loadView('reports.generateSales', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('PE.pdf');
    }
    public function generateJO(PDF $pdf, Request $request)   
    {
        $jo_no = $request->input('jo_no');

        $data = [
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
        ];

        $pdf->loadView('reports.generateJO', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('PE.pdf');
    }
    public function generatePDR(PDF $pdf, Request $request)   
    {
        $jo_no = $request->input('jo_no');
        $report = ProductionDetailReport::where('jo_no',$jo_no)->first();
        $data = [
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
            'report'        => $report
        ];

        $pdf->loadView('reports.generatePDR', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('PE.pdf');
    }

    private function saveNewUploads(PricingEstimate $estimate, Request $request): void
    {
        if (!$request->hasFile('pe_attachment')) return;

        $labels = $request->input('file_labels', []);

        foreach ($request->file('pe_attachment') as $idx => $file) {
            $path = $file->store("pricing-estimates/{$estimate->id}", 'public');

            $estimate->attachments()->create([
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


    public function updateUnitPrice(Request $request, PricingEstimateItem $item)
    {
        $validated = $request->validate([
            'price_factor' => 'required|array',
            'factor_rate' => 'required|numeric',
            'sum_charge' => 'nullable|numeric',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric', // This is the FINAL price after override/discount
            'notes' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'discount_type' => 'nullable|string|in:percentage,fixed',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        $item->update([
            'price_factor' => $validated['price_factor'],
            'factor_rate' => $validated['factor_rate'],
            'sum_charge' => $validated['sum_charge'],
            'quantity' => $validated['quantity'],
            'unit_price' => $validated['unit_price'], // Final price stored here
            'notes' => $validated['notes'],
            'internal_notes' => $validated['internal_notes'],
            'discount_type' => $validated['discount_type'] ?? null,
            'discount_amount' => $validated['discount_amount'] ?? null,
        ]);

        // return response()->json($item->refresh());
        return response()->json($item->fresh()->append('final_unit_price'));
    }

    public function updateRemarks(Request $request, PricingEstimateItem $item)
    {
        $request->validate([
            'pe_remarks' => 'nullable|string'
        ]);

        $item->update([
            'pe_remarks' => $request->input('pe_remarks')
        ]);

        return response()->json($item->refresh());
    }
    public function applyDiscount(Request $request, PricingEstimateItem $item)
    {
        $item->discount_type = $request->discount_type;
        $item->discount_amount = $request->discount_value;
        $item->save();

        return response()->json(['success' => true]);
    }
  
    public function submitForApproval(Request $request, PricingEstimate $pricing_estimate)
    {
        if ($pricing_estimate->pe_status !== 'draft') {
            return response()->json(['message' => 'Only draft estimates can be submitted.'], 422);
        }

        $pricing_estimate->update([
            'pe_status' => 'pending',
        ]);

        $adminUsers = User::role(['admin', 'super-admin'])->get();

        foreach ($adminUsers as $user) {
            notify_user(
                $user->id,
                'Pricing Estimate Submitted',
                "Pricing Estimate #{$pricing_estimate->pe_no} has been submitted for pricing.",
                [
                    'action_button' => [
                        'label' => 'View Estimate',
                        'url' => route('pricing-estimates.price-entry', $pricing_estimate->id),
                    ]
                ]
            );
        }

        return response()->json([
            'message' => 'Submitted for approval.',
            'redirect' => route('pricing-estimates.show', $pricing_estimate)
        ]);
    }

    public function priceEntry($pe_id)
    {
            $pricingEstimate = PricingEstimate::with(['pe_items', 'salesRep.employee', 'customer'])
                        ->findOrFail($pe_id);
        // optional: restrict access
        // if ($pricingEstimate->sr_id !== auth()->id()) {
        //     abort(403);
        // }

        return view('pricing_estimates.price_entry', [
            'pricingEstimate' => $pricingEstimate,
            'units' => Unit::all(),
        ]);
    }
    public function approve($pricing_estimate_id)
    {
        $pricing_estimate = PricingEstimate::findOrFail($pricing_estimate_id);
        
        if ($pricing_estimate->pe_status !== 'pending') {
            return back()->with('error', 'Only pending estimates can be approved.');
        }

        $pricing_estimate->update([
            'pe_status' => 'approved',
        ]);

        notify_user(
            $pricing_estimate->sr_id,
            'Quotation Approved',
            "PE #{$pricing_estimate->pe_no} has been approved.",
            [
                'action_button' => [
                    'label' => 'View Quotation',
                    'url' => route('pricing-estimates.edit', $pricing_estimate->id),
                ]
            ]
        );

        // Logic from generate() here...
        $quotation = Quotation::where('pricing_estimate_id', $pricing_estimate->id)
            ->latest('created_at')
            ->first();

        if (!$quotation) {
            $baseQuotes = Quotation::whereRaw("quote_no NOT LIKE '%_Rev%'")->pluck('quote_no');
            $maxQuote = $baseQuotes->map(fn ($q) => (int) $q)->max() ?? 6442;
            $newQuoteNo = $maxQuote + 1;

            $newQuotation = Quotation::create([
                'quote_no' => $newQuoteNo,
                'pricing_estimate_id' => $pricing_estimate->id,
                'quote_date' => now(),
                'quote_status' => 'pending',
                'currency_id' => $pricing_estimate->currency_id,
            ]);

            return redirect()->route('quotations.edit', $newQuotation)->with('success', 'Quotation created successfully!');
        }

        if (in_array($quotation->quote_status, ['pending', 'approved'])) {
            return redirect()->route('quotations.edit', $quotation)->with('info', 'Existing quotation loaded.');
        }

        if ($quotation->quote_status === 'rejected') {
            $baseNo = explode('_', $quotation->quote_no)[0];
            $revCount = Quotation::where('quote_no', 'LIKE', "{$baseNo}_Rev%")->count();
            $newQuoteNo = "{$baseNo}_Rev" . ($revCount + 1);

            $newQuotation = Quotation::create([
                'quote_no' => $newQuoteNo,
                'pricing_estimate_id' => $pricing_estimate->id,
                'quote_date' => now(),
                'quote_status' => 'pending',
                'currency_id' => $pricing_estimate->currency_id,
                'parent_id' => $quotation->id,
            ]);

            foreach ($quotation->quotation_items as $item) {
                $newQuotation->quotation_items()->create($item->toArray());
            }

            return to_route('quotations.edit', $newQuotation)->with('success', 'Revision quotation created.');
        }

        return redirect()->route('quotations.index')->with('error', 'Unable to generate quotation.');
    }

    public function saveDeliveryDate(Request $request, $id)
    {
        $pricing_estimate = PricingEstimate::findOrFail($id);

        $pricing_estimate->update([
            'delivery_date' => $request->input('delivery_date'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Delivery date updated successfully.',
        ]);
    }

    public function reject($pricing_estimate_id, Request $request)
    {
        $pricing_estimate = PricingEstimate::findOrFail($pricing_estimate_id);
        // dd($pricing_estimate);

        // if ($pricing_estimate->pe_status !== 'pending') {
        //     return back()->with('error', 'Only pending estimates can be rejected.');
        // }

        $validated = $request->validate([
            'revision_reason' => 'nullable|string',
        ]);

        $pricing_estimate->update([
            'pe_status' => 'draft',
        ]);
        // dd($pricing_estimate->id);
        // DB::table('pricing_estimates')
        //     ->where('id', 47)
        //     ->update([
        //         'pe_status' => 'draft',
        //     ]);
  
        // notify_user(
        //     $pricing_estimate->sr_id,
        //     'PE Disapproved',
        //     "PE #{$pricing_estimate->pe_no} has been disapproved. {$pricing_estimate->revision_reason}",
        //     [
        //         'attachment' => null,
        //         'action_button' => [
        //             'label' => 'Revise Estimate',
        //             'url' => route('pricing-estimates.edit', $pricing_estimate->id),
        //         ]
        //     ]
        // );

        return back()->with('success', 'PE has been disapproved.');
    }

    public function cancel(PricingEstimate $pricing_estimate)
    {
    if (in_array($pricing_estimate->pe_status, ['approved', 'canceled'])) {
        return back()->with('error', 'Cannot cancel an approved or already canceled PE.');
    }

    $pricing_estimate->pe_status = 'canceled';
    $pricing_estimate->save();

    return back()->with('success', 'Pricing Estimate has been canceled.');
    }

    public function addOffer(Request $request)
    {
        try {
            $validated = $request->validate([
                'pricing_estimate_id' => 'required|exists:pricing_estimates,id',
                'item_id' => 'nullable|exists:items,id', // allow linking to catalog items
                'item_name' => 'required|string|max:255',
                'item_description' => 'nullable|string',
                'unit_id' => 'required|exists:units,id',
                'quantity' => 'required|numeric|min:1',
                'unit_price' => 'required|numeric|min:0',
                'final_unit_price' => 'required|numeric|min:0',
                'discount_type' => 'nullable|string|in:percentage,fixed',
                'discount_amount' => 'nullable|numeric|min:0',
                'is_offer' => 'required|boolean',
                'parent_item_id' => 'nullable|exists:pricing_estimate_items,id',
                'pe_remarks' => 'nullable|string',
                'notes' => 'nullable|string',
                'internal_notes' => 'nullable|string',
            ]);

            $item = PricingEstimateItem::create($validated);

            // Zero out parent pricing if this is an offer
            if ($item->is_offer && $item->parent_item_id) {
                $parent = PricingEstimateItem::find($item->parent_item_id);
                if ($parent) {
                    $parent->update([
                        'quantity' => 0,
                        'unit_price' => 0,
                        'final_unit_price' => 0,
                        'discount_amount' => 0,
                        'price_factor' => null,
                        'sum_charge' => null,
                        'factor_rate' => null,
                    ]);
                }
            }

            return response()->json([
                'id' => $item->id,
                'item_id' => $item->item_id,
                'item_name' => $item->item_name,
                'description' => $item->item_description, // matches Alpine template
                'unit_id' => $item->unit_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'final_unit_price' => $item->final_unit_price,
                'discount_amount' => $item->discount_amount ?? 0,
                'discount_type' => $item->discount_type ?? 'percentage',
                'is_offer' => $item->is_offer,
                'parent_item_id' => $item->parent_item_id,
                'pe_remarks' => $item->pe_remarks,
                'notes' => $item->notes,
                'internal_notes' => $item->internal_notes,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }


}
