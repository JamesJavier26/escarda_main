<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing Preview #{{ $price_estimates->pe_no }}</title>
    <style>
        @page { size: A4; margin: 80px 15px 80px 15px; }
        header { position: fixed; top: -60px; left: 0; right: 0; text-align: center; }
        footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 80px; text-align: center; }

        body       { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 20px; font-size: 7.5pt; }
        h1,h2,h3   { margin: 0; }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .row-border-bottom { border-bottom: 2px solid black; }
        .border-bottom { border-bottom: 1px solid black !important; }
        .wrap-text { word-break: break-word; white-space: normal; max-width: 200px; }

        table { width: 100%; border-collapse: collapse; }
        td, th { border: 1px solid #ccc; padding: 6px; }
        .no-border td, .no-border th { border: none; padding: 4px 6px; }

        .watermark {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.2; font-size: 72px; color: red; z-index: 1000;
        }
        .rejection-date {
            position: fixed; bottom: 20px; right: 20px;
            font-size: 12px; opacity: 0.7; color: #666;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead" style="width:50%;height:auto;">
    </header>

    <footer>
        <div style="display:flex;align-items:flex-start;gap:10px;position:relative;">
            <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position:absolute;left:0;top:-90px;height:70px;">
            <div style="font-size:10px;width:100%;">
                <p class="text-left" style="margin:2px 0;"><strong>PE No:</strong> {{ $price_estimates->pe_no }}</p>
                <p class="text-left row-border-bottom" style="margin:2px 0;"><strong>NOTE:</strong> <i>Approval of this quotation is system generated, no signature required.</i></p>
                <p class="text-left" style="margin:2px 0;"><strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a></p>
            </div>
        </div>
    </footer>

    <main>
        <h2 class="text-center" style="margin-bottom:5px;">PRICING PREVIEW</h2>

        {{-- Customer / Quotation Info --}}
        <table class="no-border" style="width:100%;font-size:10px;line-height:1.2;">
            <tr>
                <!-- Left column -->
                <td style="width:60%;vertical-align:top;padding-right:15px;">
                    <table class="no-border" style="width:100%;">
                        <tr><td><strong>To:</strong> {{ $price_estimates->customer->name ?? '-' }}</td></tr>
                        <tr><td><strong>Address:</strong> {{ $price_estimates->customer->address ?? '-' }}</td></tr>
                        <tr><td><strong>Attention:</strong> {{ $price_estimates->attention ?? '-' }}</td></tr>
                        <tr><td><strong>RFQ/PR No.:</strong> {{ $price_estimates->pr_no ?? '-' }}</td></tr>
                    </table>
                </td>

                <td style="width:5%;">&nbsp;</td>

                <!-- Right column -->
                <td style="width:35%;vertical-align:top;">
                    <table class="no-border" style="width:100%;">
                        <tr><td><strong>No.:</strong> {{ $price_estimates->quote_number ?? '-' }}</td></tr>
                        <tr>
                            <td><strong>Date:</strong>
                                {{ $price_estimates->latest_quote
                                    ? \Carbon\Carbon::parse($price_estimates->latest_quote->created_at)->format('F d, Y')
                                    : '-' }}
                            </td>
                        </tr>
                        <tr><td><strong>Delivery:</strong> {{ $price_estimates->delivery_date ?? '-' }}</td></tr>
                        <tr><td><strong>Terms:</strong> {{ $price_estimates->terms ?? '-' }}</td></tr>
                        <tr><td><strong>Salesman:</strong> {{ $price_estimates->salesRep->employee->full_name ?? 'N/A' }}</td></tr>
                        <tr><td><strong>Currency:</strong> [{{ $price_estimates->currency->symbol }}] {{ $price_estimates->currency->name }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Items --}}
        <p style="font-weight:bold;">Please see cost breakdown as follows:</p>
        <table style="font-size:9px;line-height:1.2;">
            <thead>
                <tr>
                    <th class="text-center">Ref #</th>
                    <th class="text-center">Item #</th>
                    <th class="text-center">Qty</th>
                    <th class="text-center">Unit</th>
                    <th class="text-left">Item Description</th>
                    <th class="text-center">Unit Price</th>
                    <th class="text-center">Extended Price</th>
                </tr>
            </thead>
            <tbody>
            @php $peItems = $price_estimates->pe_items->where('is_offer',0); @endphp

            @foreach($peItems as $key=>$pe_item)
                @php
                    $offers = $price_estimates->pe_items->where('is_offer',1)->where('parent_item_id',$pe_item->id);
                    $hasOffers = $offers->isNotEmpty();
                    $discountedUnitPrice = $pe_item->final_unit_price ?? $pe_item->unit_price;
                @endphp
                <tr class="{{ $hasOffers ? '' : 'row-border-bottom' }}">
                    <td>{{ $pe_item->ref_no }}</td>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $pe_item->quantity }}</td>
                    <td>{{ $pe_item->unit->short_name }}</td>
                    <td class="wrap-text">
                        {{ $pe_item->item_description }}<br>
                        <i>{{ $pe_item->pe_remarks }}</i><br>
                        <span style="color:blue">{{ $pe_item->notes }}</span>
                    </td>
                    {{-- Unit Price --}}
                    <td class="text-center">
                        @if($pe_item->discount_amount>0)
                            <div>
                                <span style="text-decoration:line-through;color:gray;">
                                    {{ $price_estimates->currency->symbol }}{{ number_format($pe_item->unit_price,2) }}
                                </span><br>
                                <span style="font-weight:bold;">
                                    {{ $price_estimates->currency->symbol }}{{ number_format($discountedUnitPrice,2) }}
                                </span><br>
                                <span style="font-size:10px;color:green;">
                                    ({{ $pe_item->discount_type==='percentage'
                                        ? $pe_item->discount_amount.'% off'
                                        : $price_estimates->currency->symbol.number_format($pe_item->discount_amount,2).' off' }})
                                </span>
                            </div>
                        @else
                            {{ $price_estimates->currency->symbol }}{{ number_format($pe_item->unit_price,2) }}
                        @endif
                    </td>
                    {{-- Extended Price --}}
                    <td class="text-right">
                        @if($pe_item->discount_amount>0)
                            <div>
                                <span style="text-decoration:line-through;color:gray;">
                                    {{ $price_estimates->currency->symbol }}{{ number_format($pe_item->unit_price*$pe_item->quantity,2) }}
                                </span><br>
                                <span style="font-weight:bold;">
                                    {{ $price_estimates->currency->symbol }}{{ number_format($discountedUnitPrice*$pe_item->quantity,2) }}
                                </span>
                            </div>
                        @else
                            {{ $price_estimates->currency->symbol }}{{ number_format($pe_item->unit_price*$pe_item->quantity,2) }}
                        @endif
                    </td>
                </tr>

                {{-- Offers --}}
                @if($hasOffers)
                    <tr><td></td><td></td><td colspan="5" style="background:#f0f0f0;text-align:left;padding-left:100px;"><strong>List of Offers:</strong></td></tr>
                    @foreach($offers as $i=>$offer)
                        @php $offerPrice = $offer->final_unit_price ?? $offer->unit_price; @endphp
                        <tr class="{{ $loop->last ? 'row-border-bottom':'' }}">
                            <td></td><td></td>
                            <td>{{ $offer->quantity }}</td>
                            <td>{{ $offer->unit->short_name }}</td>
                            <td class="wrap-text">{{ $offer->item_description }}<br><i>{{ $offer->pe_remarks }}</i><br><span style="color:blue">{{ $offer->notes }}</span></td>
                            <td class="text-center">
                                @if($offer->discount_amount>0)
                                    <div>
                                        <span style="text-decoration:line-through;color:gray;">{{ $price_estimates->currency->symbol }}{{ number_format($offer->unit_price,2) }}</span><br>
                                        <span style="font-weight:bold;">{{ $price_estimates->currency->symbol }}{{ number_format($offerPrice,2) }}</span>
                                    </div>
                                @else
                                    {{ $price_estimates->currency->symbol }}{{ number_format($offer->unit_price,2) }}
                                @endif
                            </td>
                            <td class="text-right">{{ $price_estimates->currency->symbol }}{{ number_format($offerPrice*$offer->quantity,2) }}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
            </tbody>
        </table>

        {{-- Totals --}}
        @php
            $subtotal = $price_estimates->pe_items->sum(fn($i)=>(($i->final_unit_price ?? $i->unit_price)*$i->quantity));
            $taxStatus = $price_estimates->vat ?? $price_estimates->customer->tax_status ?? 'Zero Rated';
            $vatRate=0.12; $vatAmount=0; $subtotalExVat=$subtotal;
            if($taxStatus==='VAT Inclusive'){ $subtotalExVat=$subtotal/(1+$vatRate); $vatAmount=$subtotal-$subtotalExVat; }
        @endphp

        <table class="no-border" style="width:35%;float:right;margin-top:10px;">
            @if($taxStatus==='VAT Inclusive')
                <tr><td class="text-right"><strong>Total Vatable Sales:</strong></td><td class="text-right">{{ $price_estimates->currency->symbol }}{{ number_format($subtotalExVat,2) }}</td></tr>
                <tr><td class="text-right"><strong>VAT (12%):</strong></td><td class="text-right">{{ $price_estimates->currency->symbol }}{{ number_format($vatAmount,2) }}</td></tr>
            @endif
            <tr><td class="text-right"><strong>Total Sales Amount:</strong></td><td class="text-right border-bottom">{{ $price_estimates->currency->symbol }}{{ number_format($subtotal,2) }}</td></tr>
            <tr><td class="text-right" style="height:50px;vertical-align:bottom;"></td><td class="text-right border-bottom"></td></tr>
            <tr><td class="text-right">Noted by:</td><td class="text-right"><strong>Mrs. Maricel Escarda</strong></td></tr>
            <tr><td></td><td class="text-right"><strong>Sales Manager</strong></td></tr>
        </table>

        {{-- Remarks --}}
        <div style="clear:both;"></div>
        <table class="no-border" style="width:100%;margin-top:20px;">
            <tr><td><strong>REMARKS:</strong></td></tr>
            <tr>
                <td>{!! nl2br(e($price_estimates->quote_remarks ?? optional($price_estimates->latest_quote)->quote_remarks ?? 'No remarks yet')) !!}</td>
            </tr>
        </table>
    </main>

    @if($is_rejected)
        <div class="watermark">REJECTED</div>
        <div class="rejection-date">Rejected on: {{ $rejected_at->format('M d, Y h:i A') }}</div>
    @endif
</body>
</html>
