<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Orders Report</title>
    <style>
        @page { size: A4; margin: 80px 15px 80px 15px; }
        header { position: fixed; top: -60px; left: 0; right: 0; text-align: center; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 80px; text-align: center; }

        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 8pt; }
        h1,h2,h3 { margin: 0; }
        main { padding-bottom: 100px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 7.5pt; page-break-inside: auto; }
        thead { display: table-header-group; }
        tbody { display: table-row-group; }
        tr { page-break-inside: avoid; page-break-after: auto; }

        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background-color: #f0f0f0; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>

<header>
    <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead" style="width:50%;height:auto;">
</header>

<footer>
    <div style="font-size:10px;width:100%;">
        <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position:absolute;left:0;top:-80px;height:70px;">
        <p class="text-left" style="margin:2px 0;"><strong>NOTE:</strong> <i>This report is system generated.</i></p>
        <p class="text-left" style="margin:2px 0;"><strong>Website:</strong> 
            <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a>
        </p>
    </div>
</footer>

<main>
    <h2 class="text-center" style="margin-bottom:10px;">Job Orders Report</h2>

    <table>
        <thead>
            <tr>
                <th>JO No.</th>
                <th>Date Created</th>
                <th>Customer</th>
                <th>Sales Order</th>
                <th>Items Count</th>
                <th>Status</th>
                <th>Delivered</th>
            </tr>
        </thead>
        <tbody>
        @forelse($jobOrders as $order)
            <tr>
                <td>{{ $order->jo_no }}</td>
                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                <td>{{ $order->salesOrder?->customer?->name ?? '—' }}</td>
                <td>{{ optional($order->salesOrder)->so_no ?? '—' }}</td>
                <td>{{ $order->jo_items->count() }}</td>
                <td class="text-center">{{ ucfirst($order->jo_status) }}</td>
                <td class="text-center">{{ $order->is_delivered ? 'Yes' : 'No' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">No job orders found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</main>

</body>
</html>
