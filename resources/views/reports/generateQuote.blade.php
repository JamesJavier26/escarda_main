<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation #{{ $price_estimates->quote_number }}</title>
    <style>
        @page {
            margin: 80px 15px 80px 15px;
            
        }


        header {
            position: fixed;
            top: -60px;
            left: 0px;
            right: 0px;
            height: ;
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
        .p-1      { padding: 6px; }
        .p-2      { padding: 10px; }
        table     { width: 100%; border-collapse: collapse; }
        td, th    { border: 1px solid #ccc; padding: 8px; }
        .no-border td, .no-border th { border: none; padding: 4px 8px; }
        .border   { border: 1px solid #ccc; border-collapse: collapse; }
        .border-bottom   { border-bottom: 1px solid black !important;  }
        .row-border-bottom   { border-bottom: 2px solid black; border-collapse: collapse; }
        .w-5{width: 5%;}
        .w-10{width: 10%;}
        .w-15{width: 15%;}
        .w-20{width: 20%;}
        .w-25{width: 25%;}
        .w-30{width: 30%;}
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
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            opacity: 0.2;
            font-size: 72px;
            color: red;
            z-index: 1000;
            pointer-events: none;
        }
        .rejection-date {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.7;
            font-size: 12px;
            color: #666;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead"
            style="width: 50%; height: auto;">
    </header>
    {{-- <header>
          <img src="{{ public_path('images/logo.png') }}" alt="Logo"
         style="position: absolute; left: 120px; top: 0; height: 60px;">
        <!-- Centered Header Table -->
        <table class="no-border" style="margin: 0 auto 20px auto; width: 100%;">
            <tr>
                <td style="text-align: center; vertical-align: top;">
                    <i style="font-family: 'Lucida Sans';">Smile, God Loves You</i>
                    <h1 style="margin: 0;text-transform:uppercase;">{{ $setting->where('key','site_name')->first()->value }}</h1>
                    {!! $setting->where('key','address')->first()->value !!} 
                    <br/>{{ $setting->where('key','proprietor')->first()->value }} - Prop
                    <br/>VAT Reg. TIN: {{ $setting->where('key','tin_id')->first()->value }}
                    <br/>Tel Nos: {{ $setting->where('key','tel_no')->first()->value }}  - {{ $setting->where('key','tel_no1')->first()->value }} 
                    <br/>Telefax: {{ $setting->where('key','telefax')->first()->value }}  
                </td>
            </tr>
        </table>
    </header> --}}
    <footer>
        <div style="display: flex; align-items: flex-start; gap: 10px; position: relative;">
            {{-- Logo on the left --}}
            <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position: absolute; left: 15px; top: 0; height: 35px;">

            <div style="font-size: 10px; width: 100%;">
                {{-- Item information at the top --}}
                <div style="margin-bottom: 8px;">
                    <p style="margin: 2px 0;">&nbsp;</p>
                    <p style="margin: 2px 0;">&nbsp;</p>
                </div>

                {{-- Original content below --}}
                <p class="text-left pb-2" style="margin: 2px 0;">
                    <strong>PE No:</strong> {{ $price_estimates->pe_no }}
                </p>
                <p class="text-left row-border-bottom" style="margin: 2px 0;">
                    <strong>NOTE:</strong> <i>Approval of this quotation is system generated, no signature required.</i>
                </p>
                <p class="text-left" style="margin: 2px 0;">
                    <strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a>
                </p>
            </div>
        </div>
    </footer>


    <main style="">
        <table class="text-center"><h2>QUOTATION</h2></table>
        <table class="no-border" style="width: 100%; font-size: 10px; line-height: 1.2;">
            <tr>
                <!-- LEFT COLUMN -->
                <td style="width: 60%; vertical-align: top; padding-right: 15px;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td style="padding: 0 0 1px 0;"><strong>To:</strong> {{ $price_estimates->customer->name }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>Address:</strong> {{ $price_estimates->customer->address }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>Attention:</strong> {{ $price_estimates->attention }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>RFQ/PR NO.:</strong> {{ $price_estimates->pr_no }}</td></tr>
                    </table>
                </td>

                <!-- SPACER -->
                <td style="width: 5%;">&nbsp;</td>

                <!-- RIGHT COLUMN -->
                <td style="width: 35%; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td style="padding: 0 0 1px 15px;"><strong>NO.:</strong> {{ $price_estimates->quote_number ?? '-' }}</td></tr>
                        <tr><td style="padding: 0 0 1px 15px;"><strong>Date:</strong> {{ \Carbon\Carbon::parse($price_estimates->latest_quote->created_at)->format('F, d Y') }}</td></tr>
                        <tr><td style="padding: 0 0 1px 15px;"><strong>Delivery:</strong> {{ $price_estimates->delivery_date }}</td></tr>
                        <tr><td style="padding: 0 0 1px 15px;"><strong>Terms:</strong> {{ $price_estimates->terms }}</td></tr>
                        <tr><td style="padding: 0 0 1px 15px;"><strong>Salesman:</strong> {{ $price_estimates->salesRep->employee->full_name ?? 'N/A' }}</td></tr>
                        <tr><td style="padding: 0 0 1px 15px;"><strong>Currency:</strong> [{{ $price_estimates->currency->symbol }}] {{ $price_estimates->currency->name }}</td></tr>
                    </table>
                </td>

            </tr>
        </table>


        <!-- Item Table -->
        {{-- <h3 class="mb-2">Sir/Madam</h3> --}}
        <p style="font-weight: bold;">Please see cost breakdown as follows:</p>
        <table class="no-border" style="font-size: 9px; line-height: 1.2;">
            <thead>
                <tr>
                    <th class="text-center w-5">Ref. #</th>
                    <th class="text-center w-5">Item #</th>
                    <th class="text-center w-5">Quantity</th>
                    <th class="text-center w-5">Unit</th>
                    <th class="text-left w-25">Item Description</th>
                    <th class="text-center w-10">Unit Price</th>
                    <th class="text-center w-10">Extended Price </th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($price_estimates->pe_items as $key => $pe_item)
                    <tr class="row-border-bottom">
                        <td>{{ $pe_item->item->number }}</td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $pe_item->quantity }}</td>
                        <td>{{ $pe_item->unit->name }}</td>
                        <td class="wrap-text">
                            {{ $pe_item->item->description }}<br/>
                            <i>{{ $pe_item->remarks }}<br/></i><br/>
                            <span style="color:blue">{{ $pe_item->notes }}</span>
                        </td>
                        <td class="text-center">₱{{ $pe_item->unit_price }} / {{ $pe_item->unit->name }}</td>
                        <td class="text-right">₱{{ number_format($pe_item->unit_price * $pe_item->quantity,2) }}</td>
                    </tr>   
                @endforeach --}}
                @php
                    $peItems = $price_estimates->pe_items->where('is_offer', 0);
                @endphp

                @foreach ($peItems as $key => $pe_item)
                    @php
                        $offers = $price_estimates->pe_items
                                    ->where('is_offer', 1)
                                    ->where('parent_item_id', $pe_item->id);
                        $hasOffers = $offers->isNotEmpty();
                    @endphp

                    {{-- Main item row --}}
                    <tr class="{{ $hasOffers ? '' : 'row-border-bottom' }}">
                        <td>{{ $pe_item->ref_no ?? '' }}</td>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $pe_item->quantity }}</td>
                        <td>{{ $pe_item->unit->name }}</td>
                        <td class="wrap-text">
                            {{ $pe_item->item_description }}<br/>
                            <i>{{ $pe_item->pe_remarks }}</i><br/>
                            <span style="color:blue">{{ $pe_item->notes }}</span>
                        </td>

                        @if ($hasOffers)
                            <td class="text-center">-</td>
                            <td class="text-right">-</td>
                        @else
                            <td class="text-center">₱{{ number_format($pe_item->final_unit_price, 2) }} / {{ $pe_item->unit->name }}</td>
                            <td class="text-right">₱{{ number_format($pe_item->final_unit_price * $pe_item->quantity, 2) }}</td>
                        @endif
                    </tr>

                    {{-- Offer block --}}
                    @if ($hasOffers)
                        {{-- "List of Offers" header --}}
                        <tr >
                            <td></td>
                            <td></td>
                            <td style="background-color: #f0f0f0;" colspan="5" class="text-center font-semibold">
                                List of Offers
                            </td>
                        </tr>

                        @foreach ($offers->values() as $i => $offer)
                            @php $isLastOffer = $i === $offers->count() - 1; @endphp
                            <tr class="{{ $isLastOffer ? 'row-border-bottom' : '' }}">
                                <td></td>
                                <td></td>
                                <td>{{ $offer->quantity }}</td>
                                <td>{{ $offer->unit->name }}</td>
                                <td class="wrap-text">
                                    {{ $offer->item_description }}<br/>
                                    <i>{{ $offer->pe_remarks }}</i><br/>
                                    <span style="color:blue">{{ $offer->notes }}</span>
                                </td>
                                <td class="text-center">₱{{ number_format($offer->final_unit_price, 2) }} / {{ $offer->unit->name }}</td>
                                <td class="text-right">₱{{ number_format($offer->final_unit_price * $offer->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach




            </tbody>
        </table>
        @php
            $subtotal = $price_estimates->pe_items->sum('final_unit_price');
            $subQTY = $price_estimates->pe_items->sum('quantity');
            $partialTotal = $subtotal * $subQTY;
            $vat = $partialTotal * 0.12;
            $vatOption = $price_estimates->vat; 
        @endphp
        <table class="no-border" style="width: 35%; float:right">
            @if ($vatOption == '1') {{-- VAT Include --}}
                <tr>
                    <td class="text-right" style="padding: 2px 0;"><strong>Subtotal:</strong></td>
                    <td class="text-right" style="padding: 2px 0;">₱{{ number_format($partialTotal, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right" style="padding: 2px 0;"><strong>VAT (12%):</strong></td>
                    <td class="text-right" style="padding: 2px 0;">₱{{ number_format($vat, 2) }}</td>
                </tr>
                <tr>
                    <td class="text-right" style="padding: 2px 0;"><strong><u>VAT INCLUDED:</u></strong></td>
                    <td class="text-right border-bottom" style="padding: 2px 0;">
                        ₱{{ number_format($partialTotal + $vat, 2) }}
                    </td>
                </tr>
            @else
                <tr>
                    <td class="text-right" style="padding: 2px 0;"><strong>Total:</strong></td>
                    <td class="text-right border-bottom" style="padding: 2px 0;">
                        ₱{{ number_format($partialTotal, 2) }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="text-right" style="padding: 2px 0;">
                        @if ($vatOption == '2')
                            <strong><u>{{ config('selectOptions.vat_options.2') }}</u></strong>
                        @elseif ($vatOption == '3')
                            <strong><u>{{ config('selectOptions.vat_options.3') }}</u></strong>
                        @endif
                    </td>
                </tr>
            @endif

            {{-- Spacer before signature --}}
            <tr>
                <td class="text-right" style="padding: 2px 0; height:50px; vertical-align: bottom;"></td>
                <td class="text-right border-bottom" style="padding: 2px 0; height:50px; vertical-align: bottom;">&nbsp;</td>
            </tr>

            {{-- Signature --}}
            <tr>
                <td class="text-right" style="padding: 2px 0;">Noted by:</td>
                <td class="text-right" style="padding: 2px 0;"><strong>Mrs. Maricel Escarda</strong></td>
            </tr>
            <tr>
                <td style="padding: 2px 0;"></td>
                <td class="text-right" style="padding: 2px 0;"><strong>Sales Manager</strong></td>
            </tr>
        </table>


        <!-- REMARKS (clears float and full width) -->
        <div style="clear: both;"></div>
        <table class="no-border" style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="text-align: left; padding: 4px 0;"><strong>REMARKS:</strong></td>
            </tr>
            <tr>
                <td style="text-align: left; padding: 4px 0;">{{ $price_estimates->latest_quote->quote_remarks }}</td>
            </tr>
        </table>

        
    </main>
    @if($is_rejected)
    <div class="watermark">REJECTED </div>
    <div class="rejection-date">Rejected on: {{ $rejected_at->format('M d, Y h:i A') }}</div>
    @endif
</body>
</html>
