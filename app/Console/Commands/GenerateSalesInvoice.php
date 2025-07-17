<?php

namespace App\Console\Commands;

use App\Models\SalesOrder;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GenerateSalesInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'app:generate-sales-invoice';
    protected $signature = 'sales_invoice:generate {invoice_id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoice from template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoice = SalesOrder::with('customer', 'lineItems')->find($this->argument('invoice_id'));

        // Load template
        $templatePath = resource_path('views/invoices/template.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Replace placeholders
        $this->replacePlaceholders($sheet, [
            '{invoice_date}' => $invoice->date->format('m/d/Y'),
            '{invoice_number}' => $invoice->number,
            '{customer_name}' => $invoice->customer->name,
            '{sales_rep}' => $invoice->sales_rep,
            '{terms}' => $invoice->terms,
            '{subtotal}' => $invoice->subtotal,
            '{tax_amount}' => $invoice->tax_amount,
            '{grand_total}' => $invoice->total_amount,
        ]);

        // Handle line items
        $row = 10; // Starting row for line items
        foreach ($invoice->lineItems as $item) {
            $sheet->setCellValue("A{$row}", $item->quantity);
            $sheet->setCellValue("B{$row}", $item->description);
            $sheet->setCellValue("C{$row}", $item->unit_price);
            $sheet->setCellValue("D{$row}", $item->total_price);
            $row++;
        }

        // Save generated invoice
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $outputPath = storage_path("app/invoices/invoice_{$invoice->number}.xlsx");
        $writer->save($outputPath);

        $this->info("Invoice generated: {$outputPath}");
    }
    protected function replacePlaceholders($sheet, $replacements)
    {
        foreach ($replacements as $search => $replace) {
            $sheet->searchAndReplace($search, $replace);
        }
    }
}
