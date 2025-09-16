<?php

namespace App\Http\Controllers;

use App\Models\JobOrder;
use Illuminate\Http\Request;
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
use Barryvdh\DomPDF\PDF;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use NumberFormatter;
use function App\Helpers\notify_user;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ReportController extends Controller
{

    public function generatePricingReport(Request $request, PDF $pdf)
    {
        $query = PricingEstimate::with([
            'customer',
            'salesRep.employee',
        ])->withCount('pe_items') // this ensures pe_items_count exists
        ->where('pe_status', '!=', 'draft');

        // Search filter (PE No, Attention, Customer)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('pe_no', 'like', "%{$search}%")
                ->orWhere('attention', 'like', "%{$search}%")
                ->orWhereHas('customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
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

        // Sales Representative filter
        if ($srId = $request->input('sales_rep')) {
            $query->where('sr_id', $srId);
        }

        // Item filter (searching in pricing_estimate_items table)
        if ($item = $request->input('item')) {
            $query->whereHas('pe_items', function ($q) use ($item) {
                $q->where('item_name', 'like', "%{$item}%")
                ->orWhere('item_description', 'like', "%{$item}%");
            });
        }

        $pricingEstimates = $query->latest('updated_at')->get();

        return $pdf->loadView('reports.generatePricingReport', compact('pricingEstimates'))
                ->setPaper('a4', 'landscape')
                ->stream('pricing-estimates-report.pdf');
    }

    public function generateQuoteReport(Request $request, PDF $pdf)
    {
        $query = Quotation::with([
            'pricing_estimate.customer',
            'pricing_estimate.salesRep.employee', // <-- make sure this is here
            'salesOrder',
        ]);

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('quote_no', 'like', "%{$search}%")
                ->orWhereHas('pricing_estimate', fn ($q) =>
                    $q->where('pe_no', 'like', "%{$search}%")
                        ->orWhere('attention', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) =>
                            $c->where('name', 'like', "%{$search}%")
                        )
                );
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'to_send') {
                $query->where('quote_status', 'approved')
                    ->where('is_sent', false);
            } else {
                $query->where('quote_status', $status);
            }
        }

        // Date range filter
        if ($from = $request->input('from')) {
            $query->where('quote_date', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->input('to')) {
            $query->where('quote_date', '<=', Carbon::parse($to)->endOfDay());
        }

        // Sales Representative filter
        if ($srId = $request->input('sales_rep')) {
            $query->whereHas('pricing_estimate', fn($q) => $q->where('sr_id', $srId));
        }

        // Item filter
        if ($item = $request->input('item')) {
            $query->where(function ($q) use ($item) {
                $q->whereHas('pricing_estimate.pe_items.item', function ($sub) use ($item) {
                    $sub->where('name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%");
                });

                $q->orWhereHas('pricing_estimate.pe_items', function ($sub) use ($item) {
                    $sub->where('item_name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%");
                });
            });
        }

        $quotations = $query->latest('updated_at')->get();

        return $pdf->loadView('reports.generateQuoteReport', compact('quotations'))
                ->setPaper('a4', 'landscape')
                ->stream('quotations-report.pdf');
    }

    public function generateSalesReport(Request $request, PDF $pdf)
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Base query with eager loads
        $query = SalesOrder::with([
            'customer',
            'salesRep.employee',
            'quotation.pricing_estimate.customer',
            'quotation.pricing_estimate.salesRep.employee',
            'approver',
            'jobOrder.jo_items.item',
            'currency',
        ]);

        // Role-based visibility
        if ($user->hasRole(['super-admin', 'admin'])) {
            $query->where(function ($q) use ($userId) {
                $q->where('so_status', '!=', 'draft')
                ->orWhere('sr_id', $userId); // allow own drafts
            });
        } elseif ($user->hasRole(['operation-manager', 'sales-secretary', 'sales-manager'])) {
            $query->where('so_status', '!=', 'draft');
        } else {
            $query->where('sr_id', $userId);
        }

        // 🔍 Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('so_no', 'like', "%{$search}%")
                ->orWhere('attention', 'like', "%{$search}%")
                ->orWhere('po_no', 'like', "%{$search}%")
                ->orWhere('pr_no', 'like', "%{$search}%")
                ->orWhereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%"))
                ->orWhereHas('quotation.pricing_estimate', fn ($pe) =>
                    $pe->where('pe_no', 'like', "%{$search}%")
                        ->orWhere('attention', 'like', "%{$search}%")
                        ->orWhereHas('customer', fn ($c) =>
                            $c->where('name', 'like', "%{$search}%")
                        )
                );
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('so_status', $status);
        }

        // Date range filter
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Sales Representative filter
        if ($srId = $request->input('sales_rep')) {
            $query->where('sr_id', $srId);
        }

        // Item filter
        if ($item = $request->input('item')) {
            $query->where(function ($q) use ($item) {
                // Search in JobOrder Items
                $q->whereHas('jobOrder.jo_items.item', function ($sub) use ($item) {
                    $sub->where('name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%");
                });

                // OR directly in jo_items table
                $q->orWhereHas('jobOrder.jo_items', function ($sub) use ($item) {
                    $sub->where('item_name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%");
                });
            });
        }

        // 👉 Fetch all (no pagination for report)
        $salesOrders = $query->latest('updated_at')->get();

        // Calculate totals for report summary
        $totalOrders   = $salesOrders->count();
        $pendingOrders = $salesOrders->where('so_status', 'pending')->count();
        $approvedThisWeek = $salesOrders
            ->where('so_status', 'approved')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();
        $totalRevenue = $salesOrders->sum(function ($order) {
            if (!$order->jobOrder || !$order->jobOrder->jo_items) return 0;
            return $order->jobOrder->jo_items->sum(function ($item) {
                return ($item->unit_price ?? 0) * ($item->quantity ?? 0);
            });
        });

        return $pdf->loadView('reports.generateSalesReport', [
            'salesOrders'      => $salesOrders,
            'totalOrders'      => $totalOrders,
            'pendingOrders'    => $pendingOrders,
            'approvedThisWeek' => $approvedThisWeek,
            'totalRevenue'     => $totalRevenue,
        ])
        ->setPaper('a4', 'landscape')
        ->stream('sales-orders-report.pdf');
    }

    public function generateJOReport(Request $request, PDF $pdf)
    {
        $user = auth()->user();
        $userId = $user->id;

        // Base query with eager loads
        $query = JobOrder::with(['salesOrder.customer', 'jo_items.item']);

        // Role-based visibility (same as your index)
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

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('jo_no', 'like', "%{$search}%")
                ->orWhereHas('salesOrder.customer', fn($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            if ($status === 'to_deliver') {
                $query->where('jo_status', 'approved')
                    ->where('is_delivered', false);
            } else {
                $query->where('jo_status', $status);
            }
        }

        // Date filters
        if ($from = $request->input('from')) {
            $query->where('created_at', '>=', Carbon::parse($from)->startOfDay());
        }
        if ($to = $request->input('to')) {
            $query->where('created_at', '<=', Carbon::parse($to)->endOfDay());
        }

        // Sales Representative filter
        if ($srId = $request->input('sales_rep')) {
            $query->whereHas('salesOrder', fn($so) => $so->where('sr_id', $srId));
        }

        // Item filter
        if ($item = $request->input('item')) {
            $query->where(function ($q) use ($item) {
                $q->whereHas('jo_items.item', fn($sub) =>
                    $sub->where('name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%")
                );
                $q->orWhereHas('jo_items', fn($sub) =>
                    $sub->where('item_name', 'like', "%{$item}%")
                        ->orWhere('item_description', 'like', "%{$item}%")
                );
            });
        }

        // Fetch all for report
        $jobOrders = $query->latest('updated_at')->get();

        // Calculate totals
        $totalJobOrders     = $jobOrders->count();
        $pendingJobOrders   = $jobOrders->where('jo_status', 'pending')->count();
        $approvedJobOrders  = $jobOrders->where('jo_status', 'approved')->count();
        $deliveredJobOrders = $jobOrders->where('is_delivered', true)->count();
        $cancelledJobOrders = $jobOrders->where('jo_status', 'cancel')->count();
        $totalAmount        = $jobOrders->sum(function ($jo) {
            return $jo->jo_items->sum(fn($item) => ($item->unit_price ?? 0) * ($item->quantity ?? 0));
        });

        return $pdf->loadView('reports.generateJOReport', [
            'jobOrders'          => $jobOrders,
            'totalJobOrders'     => $totalJobOrders,
            'pendingJobOrders'   => $pendingJobOrders,
            'approvedJobOrders'  => $approvedJobOrders,
            'deliveredJobOrders' => $deliveredJobOrders,
            'cancelledJobOrders' => $cancelledJobOrders,
            'totalAmount'        => $totalAmount,
        ])
        ->setPaper('a4', 'landscape')
        ->stream('job-orders-report.pdf');
    }


    public function generatePricingPreview(PDF $pdf, Request $request, $pe_no)
    {
        if (!$pe_no) {
            abort(404, 'Missing PE No.');
        }

        $price_estimates = PricingEstimate::with([
            'customer',
            'salesRep.employee',
            'pe_items.unit',
            'currency'
        ])->where('pe_no', $pe_no)->first();

        if (!$price_estimates) {
            abort(404, 'Pricing Estimate not found.');
        }

        $data = [
            'pe_no'           => $pe_no,
            'price_estimates' => $price_estimates,
            'date'            => now()->format('F d, Y'),
            'setting'         => Setting::all(),
            'rejected_at'     => null,
            'revision_reason' => null,
            'is_rejected'     => false,
        ];

        $pdf->loadView('reports.generatePricingPreview', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Pricing Entry - #' . $pe_no . '.pdf');
    }

    public function generateQuote(PDF $pdf, Request $request, $pe_no)   // ← Laravel will inject it
    {
        
        // $pe_no = $request->input('pe_no');
        if (!$pe_no) {
            abort(404, 'Missing PE No.');
        }
        $price_estimates = PricingEstimate::with(['customer','salesRep','pe_items','currency'])->where('pe_no',$pe_no)->first();
        if (!$price_estimates) {
            abort(404, 'Pricing Estimate not found.');
        }

        // dd($price_estimates);
        $data = [
            'pe_no'        => $pe_no,
            'price_estimates' => $price_estimates,
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
            'rejected_at'   => null,
            'revision_reason' =>null,
            'is_rejected' => false,
 
        ];

        $pdf->loadView('reports.generateQuote', $data)
            // ->setPaper('a4', 'portrait');
            ->setPaper('letter', 'portrait');

        return $pdf->stream('Quotation - #' . $pe_no . '.pdf');
    }
    public function generateSales(PDF $pdf, Request $request, $so_no)   // ← Laravel will inject it
    {
        // $so_no = $request->input('so_no');
        if (!$so_no) {
            abort(404, 'Missing SO No.');
        }
        $salesOrder = SalesOrder::with(['customer', 'salesRep.employee','jobOrder.jo_items.item', 'jobOrder.jo_items.unit'])
        ->where('so_no',$so_no)
        ->latest()
        ->first();

        if (!$salesOrder) {
            abort(404, 'Sales Order not found.');
        }

        $data = [
            'so_no'        => $so_no,
            'salesOrder' => $salesOrder,
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
            'price_estimates' => PricingEstimate::with(['customer','salesRep','pe_items','currency'])->where('id',1)->first(),

        ];

        $pdf->loadView('reports.generateSales', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->stream('Sales Order - #' . $so_no . '.pdf');
    }
    public function generateInvoice(PDF $pdf, Request $request, $so_no)   // ← Laravel will inject it
    {
        // $so_no = $request->input('so_no');
        if (!$so_no) {
            abort(404, 'Missing SO No.');
        }
        $salesOrder = SalesOrder::with(['customer', 'salesRep.employee','jobOrder.jo_items.item', 'jobOrder.jo_items.unit'])
        ->where('so_no',$so_no)
        ->latest()
        ->first();

        if (!$salesOrder) {
            abort(404, 'Sales Order not found.');
        }

        $data = [
            'so_no'        => $so_no,
            'salesOrder' => $salesOrder,
            'date'         => now()->format('F d, Y'),
            'setting'      => Setting::all(),
            'price_estimates' => PricingEstimate::with(['customer','salesRep','pe_items','currency'])->where('id',1)->first(),

        ];

        $pdf->loadView('reports.generateInvoice', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Invoice - #' . $so_no . '.pdf');
    }
    public function generateJO(PDF $pdf, Request $request, $jo_no)
    {
        if (!$jo_no) {
            abort(404, 'Missing JO No.');
        }

        $jobOrder = JobOrder::with(['salesOrder.customer', 'jo_items.item.unit'])
            ->where('jo_no', $jo_no)
            ->first();

        if (!$jobOrder) {
            abort(404, 'Job Order not found.');
        }

        $data = [
            'date'      => now()->format('F d, Y'),
            'setting'   => Setting::all(),
            'jobOrder'  => $jobOrder,
        ];

        $pdf->loadView('reports.generateJO', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('JO-' . $jo_no . '.pdf');
    }
    public function generatePDR(PDF $pdf, Request $request, $id)   
    {
        $report = ProductionDetailReport::with([
            'entries',
            'jobOrder.salesOrder.customer',
            'jobOrder.jo_items.item',
            'user',
            'material'
        ])->findOrFail($id);

        $data = [
            'date'    => now()->format('F d, Y'),
            'setting' => Setting::all(),
            'report'  => $report,
        ];

        $pdf->loadView('reports.generatePDR', $data)
            ->setPaper('a4', 'portrait');

        return $pdf->stream('Production_Detail_Report_' . $report->id . '.pdf');
    }

    public function exportInvoice(SalesOrder $order)
    {
        $spreadsheet = IOFactory::load(storage_path('app/templates/sales_template.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        // Replace placeholders
        $sheet->setCellValue('C4', $order->customer->name);
        $sheet->setCellValue('H4', now()->format('m/d/Y'));
        $sheet->setCellValue('C8', $order->customer->address);
        $sheet->setCellValue('K6', $order->terms);
        $sheet->setCellValue('I6', $order->po_no);
        $sheet->setCellValue('K8', strtoupper(optional($order->salesRep->employee)->first_name ?? '—'));
        $sheet->setCellValue('I8', strtoupper($order->pr_no ?? 'N/A'));
        $sheet->setCellValue('C15', 'Attention: ' . strtoupper($order->attention ?? 'N/A'));



        $row = 18;
        $subtotal = 0;

        foreach ($order->jobOrder->jo_items as $item) {
            $total = $item->quantity * $item->unit_price;
            $sheet->setCellValue("A{$row}", $item->quantity . ' ' . $item->unit->short_name);
            $sheet->setCellValue("C{$row}", $item->item_name);
            $sheet->setCellValue("I{$row}", number_format($item->unit_price, 2));
            $sheet->setCellValue("J{$row}", number_format($total, 2));

            $subtotal += $total;
            $row += 2;
        }
        $row += 2; // Leave one blank row

        // Add centered divider
        $sheet->setCellValue("C{$row}", ' ----------nf----------');
        $row += 2; // Move to next row

        $sheet->setCellValue("C{$row}", 'Ref. DR# 6236 dtd 3/26/2025');

        $sheet->mergeCells("C{$row}:F{$row}");

        // Set font size
        $sheet->getStyle("C{$row}")->getFont()->setSize(17);

        // Align left horizontally and wrap text
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);

        // Optional: Auto height to fit wrapped content
        $sheet->getRowDimension($row)->setRowHeight(-1);
        // Subtotal
        $sheet->setCellValue('I38', number_format($subtotal, 2));

        // VAT (12% only if is_vat == 1)
        $vat = ($order->is_vat == 1) ? $subtotal * 0.12 : 0;
        $sheet->setCellValue('I44', $vat ? number_format($vat, 2) : '');

        // Grand Total (subtotal + VAT)
        $grandTotal = $subtotal + $vat;
        $sheet->setCellValue('I45', number_format($grandTotal, 2));

        // Total in Words (e.g. "Fifty One Thousand Pesos and 43/100 Only")
        $totalPesos = floor($grandTotal);
        $totalCentavos = round(($grandTotal - $totalPesos) * 100);

        $formatter = new NumberFormatter('en_PH', NumberFormatter::SPELLOUT);
        $words = ucfirst($formatter->format($totalPesos)) . ' pesos';
        if ($totalCentavos > 0) {
            $words .= ' and ' . str_pad($totalCentavos, 2, '0', STR_PAD_LEFT) . '/100';
        }
        $words .= ' only';

        $sheet->setCellValue('B45', '(PESOS) ' . strtoupper($words));


        // Save and return download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'sales_invoice_' . str_replace(['/', ' '], '_', $order->so_no) . '.xlsx';

        $filePath = storage_path("app/public/{$fileName}");
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function exportBillingInvoice(SalesOrder $order)
    {
        $spreadsheet = IOFactory::load(storage_path('app/templates/billing_invoice_template.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();


        $sheet->setCellValue('C6', $order->customer->name);
        $sheet->setCellValue('C8', $order->customer->tin_no);
        $sheet->setCellValue('B10', $order->customer->address);
        $sheet->setCellValue('C11', $order->customer->business_style);

        $sheet->setCellValue('H6', now()->format('m/d/Y'));
        $sheet->setCellValue('I8', $order->po_no);
        $sheet->setCellValue('K8', $order->terms);
        $sheet->setCellValue('I10', strtoupper($order->pr_no ?? 'N/A'));

        $sheet->setCellValue('K10', strtoupper(optional($order->salesRep->employee)->first_name ?? '—'));
        $sheet->setCellValue('C16', 'Attention: ' . strtoupper($order->attention ?? 'N/A'));


        $row = 17;
        $subtotal = 0;

        foreach ($order->jobOrder->jo_items as $item) {
            $total = $item->quantity * $item->unit_price;

            // Row for item code (above item name)
            $itemCodeRow = $row;
            $sheet->setCellValue("C{$itemCodeRow}", 'Item Code: ' . ($item->item->number ?? '—'));

            // Row for item name and pricing
            $row++; // move to next row for main item line

            // Merge A:B for quantity + unit
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->setCellValue("A{$row}", $item->quantity . ' ' . strtoupper($item->unit->short_name));

            $sheet->setCellValue("C{$row}", $item->item_name);

            // Merge H:I for unit price
            $sheet->mergeCells("H{$row}:I{$row}");
            $sheet->setCellValue("H{$row}", number_format($item->unit_price, 2));

            // Merge J:K for total
            $sheet->mergeCells("J{$row}:K{$row}");
            $sheet->setCellValue("J{$row}", number_format($total, 2));

            // Row for item description
            $descRow = $row + 1;
            $sheet->mergeCells("C{$descRow}:G{$descRow}");
            $sheet->setCellValue("C{$descRow}", $item->item_description ?? '—');

            // Style: wrap and align
            $sheet->getStyle("C{$descRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("C{$descRow}")->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            );

            // Adjust row height (larger)
            $sheet->getRowDimension($descRow)->setRowHeight(45); // higher for readability

            $subtotal += $total;

            // Space after each item block
            $row += 2; // code + main + desc = 3 rows; +1 for spacing = total +3
        }

        // Add centered divider
        $sheet->setCellValue("C{$row}", ' ----------nf----------');
        $sheet->mergeCells("C{$row}:G{$row}");
       $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);
        // Subtotal
        $sheet->setCellValue('I37', number_format($subtotal, 2));

        // VAT (12% only if is_vat == 1)
        $vat = ($order->is_vat == 1) ? $subtotal * 0.12 : 0;
        $sheet->setCellValue('I43', $vat ? number_format($vat, 2) : '');

        // Grand Total (subtotal + VAT)
        $grandTotal = $subtotal + $vat;
        $sheet->setCellValue('I45', number_format($grandTotal, 2));

        // Total in Words (e.g. "Fifty One Thousand Pesos and 43/100 Only")
        $totalPesos = floor($grandTotal);
        $totalCentavos = round(($grandTotal - $totalPesos) * 100);

        $formatter = new NumberFormatter('en_PH', NumberFormatter::SPELLOUT);
        $words = ucfirst($formatter->format($totalPesos)) . ' pesos';
        if ($totalCentavos > 0) {
            $words .= ' and ' . str_pad($totalCentavos, 2, '0', STR_PAD_LEFT) . '/100';
        }
        $words .= ' only';

        $sheet->setCellValue('B44', '(PESOS) ' . strtoupper($words));


        // Save and return download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'billing_invoice_' . str_replace(['/', ' '], '_', $order->so_no) . '.xlsx';

        $filePath = storage_path("app/public/{$fileName}");
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function exportChargeInvoice(SalesOrder $order)
    {
        $spreadsheet = IOFactory::load(storage_path('app/templates/charge_invoice_template.xlsx'));
        $sheet = $spreadsheet->getActiveSheet();

        // Replace placeholders
        $sheet->setCellValue('C9', $order->customer->name);
        $sheet->setCellValue('H9', now()->format('m/d/Y'));
        $sheet->setCellValue('C11', $order->customer->tin_no);
        $sheet->setCellValue('B13', $order->customer->address);
        $sheet->setCellValue('H11', $order->terms);
        $sheet->setCellValue('H13', $order->po_no);
        $sheet->setCellValue('H14', $order->pr_no);
        $sheet->setCellValue('J15', strtoupper(optional($order->salesRep->employee)->first_name ?? '—'));
        $sheet->setCellValue('I8', strtoupper($order->pr_no ?? 'N/A'));
        $sheet->setCellValue('C16', $order->customer->business_style);
        $sheet->setCellValue('D20', 'Attention: ' . strtoupper($order->attention ?? 'N/A'));



        $row = 23;
        $subtotal = 0;

        foreach ($order->jobOrder->jo_items as $item) {
            $total = $item->quantity * $item->unit_price;

            // Row for main item line
            $sheet->setCellValue("B{$row}", $item->quantity);
            $sheet->setCellValue("C{$row}", $item->unit->short_name);
            $sheet->setCellValue("D{$row}", $item->item_name);
            $sheet->setCellValue("H{$row}", number_format($item->unit_price, 2));
            $sheet->setCellValue("I{$row}", number_format($total, 2));

            // Row below: Item description
            $descRow = $row + 1;
            $sheet->mergeCells("D{$descRow}:G{$descRow}");
            $sheet->setCellValue("D{$descRow}", $item->item_description ?? '—');

            // Wrap and align the description
            $sheet->getStyle("D{$descRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("D{$descRow}")->getAlignment()->setVertical(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
            );

            // Optional: Adjust row height for the description
            $sheet->getRowDimension($descRow)->setRowHeight(45);

            $subtotal += $total;

            // Add spacing after each item + description
            $row += 3; // current row + desc + spacer
        }

        $row += 2; // Leave one blank row

        // Add centered divider
        $sheet->setCellValue("D{$row}", ' ----------nf----------');
        $sheet->mergeCells("D{$row}:G{$row}");
        $row += 2; // Move to next row

        $sheet->setCellValue("D{$row}", 'Ref. DR# 6236 dtd 3/26/2025');

        $sheet->mergeCells("D{$row}:G{$row}");

        // Set font size
        $sheet->getStyle("C{$row}")->getFont()->setSize(17);

        // Align left horizontally and wrap text
        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle("C{$row}")->getAlignment()->setWrapText(true);

        // Optional: Auto height to fit wrapped content
        $sheet->getRowDimension($row)->setRowHeight(-1);
        // Subtotal
        $sheet->setCellValue('I39', number_format($subtotal, 2));

        // VAT (12% only if is_vat == 1)
        $vat = ($order->is_vat == 1) ? $subtotal * 0.12 : 0;
        $sheet->setCellValue('I44', $vat ? number_format($vat, 2) : '');

        // Grand Total (subtotal + VAT)
        $grandTotal = $subtotal + $vat;
        $sheet->setCellValue('I45', number_format($grandTotal, 2));

        // Total in Words (e.g. "Fifty One Thousand Pesos and 43/100 Only")
        $totalPesos = floor($grandTotal);
        $totalCentavos = round(($grandTotal - $totalPesos) * 100);

        $formatter = new NumberFormatter('en_PH', NumberFormatter::SPELLOUT);
        $words = ucfirst($formatter->format($totalPesos)) . ' pesos';
        if ($totalCentavos > 0) {
            $words .= ' and ' . str_pad($totalCentavos, 2, '0', STR_PAD_LEFT) . '/100';
        }
        $words .= ' only';

        $sheet->setCellValue('B45', '(PESOS) ' . strtoupper($words));


        // Save and return download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $fileName = 'charge_invoice_' . str_replace(['/', ' '], '_', $order->so_no) . '.xlsx';

        $filePath = storage_path("app/public/{$fileName}");
        $writer->save($filePath);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
