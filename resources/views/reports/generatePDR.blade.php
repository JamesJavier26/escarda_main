<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Production Detail Report</title>
<style>
    @page { size: A4; margin: 80px 15px 80px 15px; }
    body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 8pt; }
    h1, h2, h3 { margin: 0; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .mt-4 { margin-top: 1.5rem; }
    .mt-2 { margin-top: 0.75rem; }
    .mb-2 { margin-bottom: 0.75rem; }
    .p-1 { padding: 4px 6px; }
    .p-2 { padding: 6px 10px; }

    table { width: 100%; border-collapse: collapse; table-layout: fixed; }
    td, th {
        border: 1px solid black;
        padding: 4px 6px;
        vertical-align: middle;
        box-sizing: border-box;
    }

    td > div, th > div {
        display: block;
        width: 100%;
        overflow-wrap: break-word;
        word-break: break-word;
        white-space: normal;
        font-size: 0.85em;
    }

    .no-border td, .no-border th { border: none; padding: 4px 6px; }
    .border-bottom { border-bottom: 1px solid black !important; }
    .row-border-bottom { border-bottom: 2px solid black; border-collapse: collapse; }

    /* Fixed column widths for top table */
    .w-3{width:3%;} .w-10{width:10%;} .w-15{width:15%;} .w-25{width:25%;} .w-40{width:40%;}

</style>
</head>
<body>

<!-- Top Info Table -->
<table class="no-border" style="margin-bottom: 20px;">
<tr>
    <td colspan="6" class="text-center"><div><strong>PRODUCTION DETAIL REPORT</strong></div></td>
</tr>
<tr>
    <td class="w-25"><div>Date</div></td>
    <td class="w-3"><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ $report->entries->isNotEmpty() ? \Carbon\Carbon::parse($report->entries->first()->date_production)->format('F d, Y') : '' }}</div></td>
    <td class="w-3"></td>
    <td class="w-40"><div>Beginning Weight of Materials</div></td>
</tr>
<tr>
    <td><div>J.O</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ $report->jo_no }}</div></td>
    <td></td>
    <td class="border-bottom"><div>{{ $report->entries->first()->beginning_weight ?? 0 }} g</div></td>
</tr>
<tr>
    <td><div>Company Name</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ optional($report->jobOrder->salesOrder->customer)->name ?? 'N/A' }}</div></td>
    <td></td>
    <td><div>Ending Weight of Material</div></td>
</tr>
<tr>
    <td><div>Item</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ optional($report->jobOrder->jo_items->first()->item)->name ?? 'N/A' }}</div></td>
    <td></td>
    <td class="border-bottom"><div>{{ $report->entries->last()->ending_weight ?? 0 }} g</div></td>
</tr>
<tr>
    <td><div>Quantity</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ $report->entries->sum(fn($e) => (float)($e->quantity_made ?? 0)) }}</div></td>
    <td></td>
    <td><div>Raw Material / Salang</div></td>
</tr>
<tr>
    <td><div>Material</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ optional($report->jobOrder->jo_items->first()->material)->name ?? 'N/A' }}</div></td>
    <td></td>
    <td class="border-bottom"><div>{{ $report->entries->sum(fn($e) => (float)($e->raw_material_weight_salang ?? 0)) }} g</div></td>
</tr>
<tr>
    <td><div>Hardness</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ optional($report->jobOrder->jo_items->first())->hardness ?? 'N/A' }}</div></td>
    <td></td>
    <td><div>Remarks</div></td>
</tr>
<tr>
    <td><div>Press</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ $report->press }}</div></td>
    <td></td>
    <td class="border-bottom"><div>{{ $report->remarks ?? 'N/A' }}</div></td>
</tr>
<tr>
    <td><div>Pressman</div></td>
    <td><div>:</div></td>
    <td colspan="2" class="border-bottom"><div>{{ optional($report->user)->username ?? 'N/A' }}</div></td>
    <td></td>
    <td class="border-bottom"><div></div></td>
</tr>

</table>

<!-- Bottom Entries Table -->
<table style="margin-top: 20px;">
<tr>
    <th class="text-center w-15"><div>Date</div></th>
    <th class="text-center w-10"><div>Quantity</div></th>
    <th class="text-center w-10"><div>Reject</div></th>
    <th class="text-center w-15"><div>Temperature</div></th>
    <th class="text-center w-10"><div>Time</div></th>
    <th class="text-center w-10"><div>Salang #</div></th>
    <th class="text-center w-30"><div>Remarks</div></th>
</tr>

@foreach($report->entries->sortBy('salang_number') as $entry)
<tr>
    <td class="text-center border-bottom"><div>{{ \Carbon\Carbon::parse($entry->date_production)->format('F d, Y') }}</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->quantity_made ?? 0 }}</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->reject_quantity ?? 0 }}</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->temperature ?? 'N/A' }} °C</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->time_per_salang ?? 'N/A' }}</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->salang_number }}</div></td>
    <td class="text-center border-bottom"><div>{{ $entry->remarks ?? 'N/A' }}</div></td>
</tr>
@endforeach

</table>

</body>
</html>
