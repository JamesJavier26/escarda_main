<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing Estimate</title>
    <style>
        @page {
            size: A4;
            margin: 80px 15px 80px 15px;
        }

        body       { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 7.5pt; }
        h1, h2, h3 { margin: 0; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .mt-4     { margin-top: 1.5rem; }
        .mt-2     { margin-top: 0.75rem; }
        .mb-2     { margin-bottom: 0.75rem; }
        .p-1      { padding: 6px; }
        .p-2      { padding: 10px; }
        table     { width: 100%; border-collapse: collapse; }
        td, th    { border: 1px solid black;  }
        .no-border td, .no-border th { border: none; padding: 4px 8px; }
        .border   { border: 1px solid #ccc; border-collapse: collapse; }
        .border-bottom   { border-bottom: 1px solid black !important;  }
        .row-border-bottom   { border-bottom: 2px solid black; border-collapse: collapse; }
        .w-3{width:3%;}
        .w-5{width: 5%;}
        .w-10{width: 10%;}
        .w-15{width: 15%;}
        .w-20{width: 20%;}
        .w-25{width: 25%;}
        .w-30{width: 30%;}
        .w-40{width: 40%;}
        .wrap-text {
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
            max-width: 200px;
        }
        .p-2{
            padding: 2;
        }
        .pb-2{
            padding-bottom: 0.75rem;
        }
        .ch{
            padding: 2px;
        }
    </style>
</head>
<body>
       <table class="no-border" style="margin: 0 auto 20px auto; width: 100%;">
            <tr> <td colspan="6" class="text-center"><strong>PRODUCTION DETAIL REPORT</strong></td> </tr>
            <tr>
                <td class="ch w-25">Date</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ \Carbon\Carbon::parse($report->date_production)->format('F d, Y') }}</td>
                <td class="w-3"></td>
                <td class="w-40">Beginning Weight of Materials</td>
            </tr>
            <tr>
                <td class="ch w-25">J.O</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom ">{{ $report->jo_no }}</td>
                <td class="w-3"></td>
                <td class="border-bottom">{{ $report->beginning }}</td>
            </tr>
            <tr>
                <td class="ch w-25">Company Name</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->jobOrder->salesOrder->customer->name }}</td>
                <td class="w-3"></td>
                <td class="">Ending Weight of Material</td>
            </tr>
            <tr>
                <td class="ch w-25">Item Description</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->jobOrder->jo_items->first()->item->name ?? 'N/A' }}</td>
                <td class="w-3"></td>
                <td class="border-bottom">{{ $report->ending }}</td>
            </tr>
            <tr>
                <td class="ch w-25">Quantity</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->quantity }}</td>
                <td class="w-3"></td>
                <td class="">Raw Material / Salang</td>
            </tr>
            <tr>
                <td class="ch w-25">Material</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->material->name }}</td>
                <td class="w-3"></td>
                <td class="border-bottom">{{ $report->raw_material }}</td>
            </tr>
            <tr>
                <td class="ch w-25">Hardness</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->hardness }}</td>
                <td class="w-3"></td>
                <td class="">Actual Weight of Production / unit</td>
            </tr>
            <tr>
                <td class="ch w-25">Temperature</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->temperature }}</td>
                <td class="w-3"></td>
                <td class="border-bottom">{{ $report->actual_weight }}</td>
            </tr>
            <tr>
                <td class="ch w-25">Time</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->time }}</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">Press</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->press }}</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">Pressman</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom">{{ $report->employee->full_name }}</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">&nbsp;</td>
                <td class="w-3"></td>
                <td colspan="2" class=""></td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">&nbsp;</td>
                <td class="w-3"></td>
                <td colspan="2" class=""></td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">Day</td>
                <td class="w-3">:</td>
                <td colspan="2" class="border-bottom ">{{ $report->day }}</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">&nbsp;</td>
                <td class="w-3"></td>
                <td colspan="2" class=""></td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25">&nbsp;</td>
                <td class="w-3"></td>
                <td colspan="2" class=""></td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25 text-right" colspan="2">QUANTITY MADE / SHIFT</td>
                {{-- <td class="w-3"></td> --}}
                <td colspan="2" class=""></td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25 text-center">Date</td>
                <td class="w-3"></td>
                <td class="text-center">Quantity</td>
                <td class="text-center">Reject</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
            <tr>
                <td class="ch w-25 text-center border-bottom">{{ \Carbon\Carbon::parse($report->made_date)->format('F d, Y') }}</td>
                <td class="w-3"></td>
                <td class="border-bottom text-center">{{ $report->made_quantity }}</td>
                <td class="border-bottom text-center">{{ $report->made_reject }}</td>
                <td class="w-3"></td>
                <td class="border-bottom"></td>
            </tr>
        </table>
</body>
</html>
