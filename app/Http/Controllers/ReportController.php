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
use Illuminate\Support\Facades\Storage;
use NumberFormatter;
use function App\Helpers\notify_user;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class ReportController extends Controller
{
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
