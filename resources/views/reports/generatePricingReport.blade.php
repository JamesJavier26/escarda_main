<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing Estimates Report</title>
    <style>
        @page { size: A4; margin: 80px 15px 80px 15px; }
        header { position: fixed; top: -60px; left: 0; right: 0; text-align: center; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 80px; text-align: center; }

        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 7.5pt; }
        h1,h2,h3 { margin: 0; }
        main { padding-bottom: 100px; } /* extra space so table doesn't collide with footer */

        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 8pt; page-break-inside: auto; }
        thead { display: table-header-group; } /* repeat header on each PDF page */
        tbody { display: table-row-group; }
        tr { page-break-inside: avoid; page-break-after: auto; }

        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .no-border td, .no-border th { border: none; padding: 4px 6px; }
        .row-border-bottom { border-bottom: 2px solid black; }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead" style="width:50%;height:auto;">
</header>

<footer>
    <div style="display:flex;align-items:flex-start;gap:10px;position:relative;">
        <div style="font-size:10px;width:100%;">
            <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position:absolute;left:0;top:-80px;height:70px;">
            <p class="text-left" style="margin:2px 0;"><strong>Pricing Estimates Report</strong></p>
            <p class="text-left row-border-bottom" style="margin:2px 0;"><strong>NOTE:</strong> <i>This report is system generated.</i></p>
            <p class="text-left" style="margin:2px 0;"><strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a></p>
        </div>
    </div>
</footer>

<main>
    <h2 class="text-center" style="margin-bottom:10px;">Pricing Estimates Report</h2>

    <table>
        <thead>
            <tr>
                <th>PE No.</th>
                <th>Customer</th>
                <th>Attention</th>
                <th>Delivery</th>
                <th>Terms</th>
                <th>Total Items</th>
                <th>Sales Representative</th>
                <th>Status</th>
                <th>Date Created</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pricingEstimates as $pe)
            <tr>
                <td>{{ $pe->pe_no }}</td>
                <td>{{ $pe->customer->name ?? '-' }}</td>
                <td>{{ $pe->attention ?? '-' }}</td>
                <td>{{ $pe->delivery_date ?? '-' }}</td>
                <td>{{ $pe->terms ?? '-' }}</td>
                <td class="text-center">{{ $pe->pe_items_count }}</td>
                <td>{{ optional($pe->salesRep->employee)->full_name ?? '-' }}</td>
                <td>{{ ucfirst($pe->pe_status) ?? '-' }}</td>
                <td>{{ $pe->created_at->format('M j, Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</main>

</body>
</html>
