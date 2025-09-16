<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Temporary Quotation #{{ $temporaryQuotation->id }}</title>
    <style>
        @page {
            margin: 80px 15px 80px 15px;
        }

        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            text-align: center;
        }

        footer {
            position: fixed; 
            bottom: -40px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
        }

        body       { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 7.5pt; }
        h1, h2, h3 { margin: 0; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .mt-4     { margin-top: 1.5rem; }
        .mt-2     { margin-top: 0.75rem; }
        .mb-2     { margin-bottom: 0.75rem; }
        table     { width: 100%; border-collapse: collapse; }
        td, th    { border: 1px solid #ccc; padding: 6px; }
        .no-border td, .no-border th { border: none; padding: 4px 8px; }
        .border-bottom { border-bottom: 1px solid black !important; }
        .row-border-bottom { border-bottom: 2px solid black; }
        .wrap-text { word-wrap: break-word; white-space: normal; max-width: 200px; }
        .pb-2 { padding-bottom: 0.75rem; }
        .w-5{width: 5%;}
        .w-10{width: 10%;}
        .w-15{width: 15%;}
        .w-20{width: 20%;}
        .w-25{width: 25%;}
        .w-30{width: 30%;}
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead"
            style="width: 50%; height: auto;">
    </header>

    <footer>
        <div style="font-size: 10px; width: 100%;">

            {{-- Logo on the left --}}
            <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position: absolute; left: 0px; top: -70px; height: 70px;">

            <p class="text-left pb-2" style="margin: 2px 0;">
                <strong>TQ No:</strong> {{ $temporaryQuotation->id }}
            </p>
            <p class="text-left row-border-bottom" style="margin: 2px 0;">
                <strong>NOTE:</strong> <i>This is a system generated quotation, no signature required.</i>
            </p>
            <p class="text-left" style="margin: 2px 0;">
                <strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a>
            </p>
        </div>
    </footer>

    <main>
        <h2 style="text-align: center; margin-bottom: 5px;">TEMPORARY QUOTATION</h2>

        <table class="no-border" style="width: 100%; font-size: 10px; line-height: 1.2;">
            <tr>
                <!-- LEFT COLUMN -->
                <td style="width: 60%; vertical-align: top; padding-right: 15px;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td><strong>To:</strong> {{ $temporaryQuotation->customer->name ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Address:</strong> {{ $temporaryQuotation->customer->address ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Attention:</strong> {{ $temporaryQuotation->attention }}</td></tr>
                        <tr><td><strong>PR NO.:</strong> {{ $temporaryQuotation->pr_no }}</td></tr>
                    </table>
                </td>

                <!-- RIGHT COLUMN -->
                <td style="width: 35%; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td><strong>Date:</strong> {{ \Carbon\Carbon::parse($temporaryQuotation->created_at)->format('F d, Y') }}</td></tr>
                        <tr><td><strong>Delivery:</strong> {{ $temporaryQuotation->delivery_date ?? '-' }}</td></tr>
                        <tr><td><strong>Terms:</strong> {{ $temporaryQuotation->terms }}</td></tr>
                        <tr><td><strong>Salesman:</strong> {{ $temporaryQuotation->salesRep->username ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Currency:</strong> [{{ $temporaryQuotation->currency->symbol ?? '' }}] {{ $temporaryQuotation->currency->name ?? '' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <p style="font-weight: bold; margin-top: 10px;">Please see cost breakdown as follows:</p>
        <table style="font-size: 9px; line-height: 1.2;">
            <thead>
                <tr>
                    <th class="text-center w-5">#</th>
                    <th class="text-center w-10">Quantity</th>
                    <th class="text-center w-10">Unit</th>
                    <th class="text-left w-30">Item Description</th>
                    <th class="text-center w-15">Unit Price</th>
                    <th class="text-center w-15">Extended Price</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach ($temporaryQuotation->items as $index => $item)
                    @php 
                        $lineTotal = $item->quantity * $item->unit_price; 
                        $subtotal += $lineTotal;
                    @endphp
                    <tr class="row-border-bottom">
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->unit->short_name ?? 'N/A' }}</td>
                        <td class="wrap-text">{{ $item->item_name }} <br><i>{{ $item->item_description }}</i></td>
                        <td class="text-center">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">₱{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @php
            $taxStatus = $temporaryQuotation->vat ?? 'Zero Rated';
            $vatRate = 0.12;
            $vatAmount = 0;
            $subtotalExVat = $subtotal;

            if ($taxStatus === 'VAT Inclusive') {
                $subtotalExVat = $subtotal / (1 + $vatRate);
                $vatAmount = $subtotal - $subtotalExVat;
            }
        @endphp

        <table class="no-border" style="width: 35%; float:right; margin-top: 15px;">
            @if ($taxStatus === 'VAT Inclusive')
                <tr>
                    <td class="text-right"><strong>Total Vatable Sales:</strong></td>
                    <td class="text-right">₱{{ number_format($subtotalExVat, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right"><strong>VAT (12%):</strong></td>
                    <td class="text-right">₱{{ number_format($vatAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right"><strong><u>Total Sales Amount:</u></strong></td>
                    <td class="text-right border-bottom">₱{{ number_format($subtotal, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td class="text-right"><strong>Zero Rated Amount:</strong></td>
                    <td class="text-right">₱{{ number_format($subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Total Sales Amount:</strong></td>
                    <td class="text-right border-bottom">₱{{ number_format($subtotal, 2) }}</td>
                </tr>
            @endif
        </table>

        <div style="clear: both;"></div>

        <!-- Remarks -->
    <table class="no-border" style="width: 100%; margin-top: 20px;">
        <tr><td><strong>REMARKS:</strong></td></tr>
        <tr>
            <td>{!! nl2br(e($temporaryQuotation->remarks ?? 'No remarks yet')) !!}</td>
        </tr>
    </table>
    </main>
</body>
</html>
