<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Order #{{ $salesOrder->so_no }}</title>
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
            bottom: -50px; 
            left: 0px; 
            right: 0px;
            height: 50px; 
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
        td, th    { border: 1px solid black;  }
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
    </style>
</head>
<body>
    <header>
        <img src="{{ public_path('images/letterhead.png') }}" alt="Letterhead"
            style="width: 50%; height: auto;">
    </header>
    {{-- <footer>
        <p class="text-left row-border-bottom pb-2">
            <strong>NOTE:</strong> <i>Approval of this quotation is system generated, no signature required.</i>
        </p>
        <p class="text-left">
            <strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a>
        </p>
    </footer> --}}

    <main>
        <table class="text-center"><h2>SALES ORDER</h2></table>
                <table class="no-border" style="width: 100%; font-size: 10px; line-height: 1.2;">
            <tr>
                <td style="width: 40%; vertical-align: top; padding-right: 20px;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td style="padding: 0 0 1px 0;">&nbsp;</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>CUSTOMER:</strong> {{ $salesOrder->customer->name }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>ADDRESS:</strong> {{ $salesOrder->customer->address }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>CONTACT PERSON:</strong> </td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>SALES REP.:</strong>   {{ optional($salesOrder->salesRep->employee)->full_name ?? '—' }} </td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>P.R. NO:</strong> {{ $salesOrder->pr_no }} </td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>Quotation #:</strong>  {{ $salesOrder->quotation->quote_no ?? '-' }}</td></tr>
                    </table>
                </td>
                <td style="width: 20%;">&nbsp;</td>
                <td style="width: 40%; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td class="text-center" style="padding: 0 0 1px 0;"><strong>NO. {{ $salesOrder->so_no }}</strong></td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>DATE ORDERED:</strong> {{ $salesOrder->date_ordered }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>DATE OF DELIVERY:</strong> {{ $salesOrder->delivery_date }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>TEL. NO:</strong> {{ $salesOrder->customer->telno }}</td></tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>J.O. NO:</strong> {{ $salesOrder->jobOrder->jo_no }}</td> </tr>
                        <tr><td style="padding: 0 0 1px 0;"><strong>P.O. NO:</strong> {{ $salesOrder->po_no }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        @php
            $total = 0;
            foreach ($salesOrder->jobOrder->jo_items as $item) {
                $total += $item->unit_price * $item->quantity;
            }

            $isVat = $salesOrder->is_vat == 1;
            $vatAmount = $isVat ? $total * 0.12 : 0;
            $grandTotal = $total + $vatAmount;
        @endphp

        <table class="">
            <thead>
                <tr>
                    <th class="text-center w-5">ITEM CODE</th>
                    <th class="text-center w-5">QTY</th>
                    <th class="text-center w-25">DESCRIPTION</th>
                    <th class="text-center w-10">UNIT PRICE</th>
                    <th class="text-left w-10">TOTAL PRICE</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($salesOrder->jobOrder->jo_items as $item)
                    @php
                        $lineTotal = $item->unit_price * $item->quantity;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $item->item->number ?? '—' }}</td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->unit->name }}</td>
                        <td class="wrap-text text-center">{{ $item->item_description ?? '—' }}</td>
                        <td class="text-center">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-center">₱{{ number_format($lineTotal, 2) }}</td>
                    </tr>
                @endforeach
                @php
                    $blankRows = 3;
                @endphp

                @for ($i = 0; $i < $blankRows; $i++)
                    <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                @endfor


                {{-- Totals --}}
                <tr>
                    <td></td><td></td><td></td>
                    <td class="text-center font-semibold">TOTAL SALES</td>
                    <td class="text-center">₱{{ number_format($total, 2) }}</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td class="text-center font-semibold">VAT 12%</td>
                    <td class="text-center">₱{{ number_format($vatAmount, 2) }}</td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td class="text-center font-bold">TOTAL AMOUNT</td>
                    <td class="text-center font-bold">₱{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>


        <table class="no-border" style="width: 100%; margin-top:30px;">
            <tr>
                <td class="text-left" style="padding: 2px 0;"><strong>REMARKS:</strong></td>
                <td style="padding: 2px 0;"></td>
            </tr>
            <tr>
                <td colspan="2">{!! $salesOrder->remarks !!}</td>
            </tr>
        </table>
        <table style="margin-top:100px">
            <tr>
                <td colspan="3" class="text-center"><h1>COMMENT</h1></td>
            </tr>
            <tr>
                <td class="text-center" style="font-weight: bold">MESSAGE</td>
                <td class="text-center" style="font-weight: bold">DATE & TIME</td>
                <td class="text-center" style="font-weight: bold">SENDER</td>
            </tr>
            @foreach ($salesOrder->comments as $comment)
                <tr>
                    <td class="text-center">{{ $comment->comment }}</td>
                    <td class="text-center">{{ $comment->created_at->format('F d, Y h:i:s') }}</td>
                    <td class="text-center">J. Santos</td>
                </tr>
            @endforeach
            {{-- <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr> --}}
        </table>
       <table class="no-border" style="width: 100%;">
            <tr>
                <td class="text-left w-10" style="padding: 2px 0;"><strong>APPROVED:</strong></td>
                <td style="padding: 2px 0;">Yes (6/20/2025 05:50:50 PM)</td>
            </tr>
        </table>
       <table class="no-border" style="width: 100%;">
            <tr>
                <td class="text-center" style="padding: 2px 0;"><strong>DELIVERY DETAILS:</strong></td>
            </tr>
            <tr>
                <td class="text-center"></td>
            </tr>
        </table>
    </main>
</body>
</html>
