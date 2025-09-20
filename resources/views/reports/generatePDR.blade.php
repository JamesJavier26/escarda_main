<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Production Detail Report</title>
<style>
    @page { size: A4; margin: 80px 15px 80px 15px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; margin: 0; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    td, th { border: 1px solid black; padding: 4px 6px; vertical-align: top; }
    .no-border td { border: none; padding: 2px 4px; }
    .text-center { text-align: center; }
    .page-break { page-break-before: always; }
    header { position: fixed; top: -60px; left: 0; right: 0; text-align: center; }
    footer { position: fixed; bottom: -50px; left: 0; right: 0; text-align: center; font-size: 9px; }
</style>
</head>
<body>

<header>
    <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead" style="width: 50%; height: auto;">
</header>

<footer>
    <div style="position: relative;">
        <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position: absolute; left: 0; top: -30px; height: 70px;">
        <div style="margin-left: 80px;">
            <p style="margin: 2px 0;"><strong>NOTE:</strong> <i>Approval of this report is system generated, no signature required.</i></p>
            <p style="margin: 2px 0;"><strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a></p>
        </div>
    </div>
</footer>

<main>

<!-- Report Header -->
<table class="no-border">
    <tr>
        <td colspan="4" class="text-center"><strong>PRODUCTION DETAIL REPORT</strong></td>
    </tr>
    <tr>
        <td><strong>J.O No.:</strong></td>
        <td>{{ $report->jo_no }}</td>
        <td><strong>Date:</strong></td>
        <td>{{ $report->entries->isNotEmpty() ? \Carbon\Carbon::parse($report->entries->first()->production_date)->format('F d, Y') : '' }}</td>
    </tr>
    <tr>
        <td><strong>Company Name:</strong></td>
        <td>{{ optional($report->jobOrder->salesOrder->customer)->name ?? 'N/A' }}</td>
        <td><strong>Item:</strong></td>
        <td>{{ optional($report->jobOrder->jo_items->first())->item_name ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Material:</strong></td>
        <td>{{ optional($report->jobOrder->jo_items->first()->material)->name ?? 'N/A' }}</td>
        <td><strong>Hardness:</strong></td>
        <td>{{ optional($report->jobOrder->jo_items->first())->hardness ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Press:</strong></td>
        <td>{{ $report->press ?? 'N/A' }}</td>
        <td><strong>Pressman:</strong></td>
        <td>{{ optional($report->user)->username ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td><strong>Remarks:</strong></td>
        <td colspan="3">{{ $report->remarks ?? '-' }}</td>
    </tr>
</table>

@php $entriesPerPage = 3; @endphp

@foreach($report->entries as $index => $entry)
<table>
    <tr>
        <td colspan="4" class="text-center"><strong>Entry #{{ $loop->iteration }}</strong></td>
    </tr>
    <tr>
        <td><strong>Date</strong></td>
        <td>{{ \Carbon\Carbon::parse($entry->production_date)->format('F d, Y') }}</td>
        <td><strong>Start Time</strong></td>
        <td>{{ $entry->start_time ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Temp Setting</strong></td>
        <td>{{ $entry->temp_setting ?? '-' }}</td>
        <td><strong>Process Duration</strong></td>
        <td>{{ $entry->process_duration ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Beginning Weight</strong></td>
        <td>{{ $entry->beginning_weight_rawmat ?? 0 }}</td>
        <td><strong>Ending Weight</strong></td>
        <td>{{ $entry->ending_weight_rawmat ?? 0 }}</td>
    </tr>
    <tr>
        <td><strong>Qty Produced</strong></td>
        <td>{{ $entry->daily_output ?? 0 }}</td>
        <td><strong>Rejects</strong></td>
        <td>{{ $entry->reject_quantity ?? 0 }}</td>
    </tr>
    <tr>
        <td><strong>Press No.</strong></td>
        <td>{{ $entry->press_no ?? '-' }}</td>
        <td><strong>Actual Finish Wt</strong></td>
        <td>{{ $entry->actual_finish_product_weight ?? 0 }}</td>
    </tr>
    <tr>
        <td><strong>Remaining Balance</strong></td>
        <td>{{ $entry->remaining_balance ?? 0 }}</td>
        <td><strong>Insert Details</strong></td>
        <td>{{ $entry->insert_details ?? '-' }}</td>
    </tr>
    <tr>
        <td><strong>Thickness</strong></td>
        <td>{{ $entry->thickness ?? 0 }}</td>
        <td><strong>Rawmat Size</strong></td>
        <td>{{ $entry->rawmat_size_salang ?? 0 }}</td>
    </tr>
    <tr>
        <td><strong>Rawmat Weight</strong></td>
        <td>{{ $entry->rawmat_weight_salang ?? 0 }}</td>
        <td><strong>Overflow Weight</strong></td>
        <td>{{ $entry->overflow_weight_salang ?? 0 }}</td>
    </tr>
    <tr>
        <td colspan="4"><strong>Remarks:</strong> {{ $entry->remarks ?? '-' }}</td>
    </tr>
</table>

@if(($loop->iteration % $entriesPerPage) == 0 && !$loop->last)
<div class="page-break"></div>
@endif
@endforeach

</main>
</body>
</html>
