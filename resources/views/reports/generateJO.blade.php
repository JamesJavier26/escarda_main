<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Order #{{ $jobOrder->jo_no }}</title>
    <style>
        @page {
            size: A4;
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
        .w-3{width: 3%;}
        .w-5{width: 5%;}
        .w-8{width: 8%;}
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
    <footer>
            <div style="display: flex; align-items: flex-start; gap: 10px; position: relative;">
                {{-- Logo on the left --}}
                <img src="{{ public_path('images/smile.png') }}" alt="Logo" style="position: absolute; left: 0px; top: -30px; height: 70px;">

                <div style="font-size: 10px; width: 100%;">
                    {{-- Item information at the top --}}
                    <div style="margin-bottom: 8px;">
                        <p style="margin: 2px 0;">&nbsp;</p>
                        <p style="margin: 2px 0;">&nbsp;</p>
                    </div>

                    {{-- Original content below --}}
                    <p class="text-left row-border-bottom" style="margin: 2px 0;">
                        <strong>NOTE:</strong> <i>Approval of this quotation is system generated, no signature required.</i>
                    </p>
                    <p class="text-left" style="margin: 2px 0;">
                        <strong>Website:</strong> <a href="http://www.escardastrading.com" target="_blank">http://www.escardastrading.com</a>
                    </p>
                </div>
            </div>
        </footer>

    <main>
        <table class="text-center"><h2>JOB ORDER</h2></table>
        <table class="no-border" style="width: 100%;">
            <tr>
                <td style="width: 40%; vertical-align: top; padding-right: 20px;">
                    <table class="no-border" style="width: 100%;">
                        <tr><td style="padding: 2px 0;">&nbsp;</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>DATE:</strong> {{ $date }}</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>CUSTOMER:</strong> {{ $jobOrder->salesOrder->customer->name ?? '—' }}</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>SALES REP:</strong>  {{ optional($jobOrder->salesOrder->salesRep->employee)->full_name ?? '—' }}</td></tr>
                    </table>
                </td>

                <td style="width: 20%;">&nbsp;</td>

                <td style="width: 40%; vertical-align: top;">
                    <table class="no-border" style="width: 100%;">
                        <tr>
                            <td style="padding: 2px 0;">
                                <strong>JO NO. {{ $jobOrder->jo_no }}</strong>
                            </td>
                        </tr>
                        <tr><td style="padding: 2px 0;"><strong>P.R. #:</strong> {{ $jobOrder->salesOrder->pr_no ?? '—' }}</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>P.O #:</strong> {{ $jobOrder->salesOrder->po_no ?? '—' }}</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>S.O #:</strong> {{ $jobOrder->salesOrder->so_no ?? '—' }}</td></tr>
                        <tr><td style="padding: 2px 0;"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($jobOrder->due_date)->format('F d, Y') }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>


        <table class="">
            <thead>
                <tr>
                    <th class="text-center w-3">NO</th>
                    <th class="text-center w-8">ITEM NUMBER</th>
                    <th class="text-center w-5">QUANTITY</th>
                    <th class="text-center w-25">DESCRIPTION</th>
                    <th class="text-left w-10">STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobOrder->jo_items as $index => $joItem)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $joItem->item->number ?? '—' }}</td>
                        <td class="text-center">{{ $joItem->quantity }} {{ $joItem->unit->short_name ?? '' }}</td>
                       <td class="align-top">
                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $joItem->item_description }}</div>
                            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400" style="margin-top: 15px">
                                <strong>Rubber Material:</strong> {{ $joItem->material->name ?? '—' }},
                                <strong>Hardness:</strong>  {{ $joItem->hardness ?? '—' }},
                                <strong>Thickness:</strong>  {{ $joItem->thickness ?? '—' }}{{ $joItem->thickness_unit ? ' ' . $joItem->thickness_unit : '' }},
                                @if(!empty($joItem->note))
                                    <strong>Trade:</strong> {{ $joItem->note }}
                                @else
                                    <strong>Mould:</strong> {{ $joItem->new_mould_name ?: ($joItem->mould->name ?? '—') }}
                                @endif
                                    @if($joItem->is_linear)
                                    , <strong>MOP:</strong> Linear: {{ $joItem->total_length ?? '—' }}
                                @endif
                            </div>
                        </td>

                        <td style="vertical-align: text-top">
                            {{ $joItem->quantity }} {{ $joItem->unit->short_name ?? '' }}
                            <br/><br/><br/>
                            <i>Ready to be produced!</i>
                        </td>
                    </tr>   
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500">No job order items found.</td>
                    </tr>
                @endforelse

 
            </tbody>
        </table>


        {{-- <p class="row-border-bottom"></p> --}}
        <table class="no-border" style="width: 100%; padding-right: 30px;">
            <tr class="row-border-bottom">
                <td class="text-left" style="padding: 2px 0; height: 100px; vertical-align: top;">
                    <strong>REMARKS:</strong><br>
                    {!! nl2br(e($jobOrder->remarks ?? '—')) !!}
                </td>
            </tr>
            <tr class="row-border-bottom">
                <td class="text-left" style="padding: 2px 0; height: 100px; vertical-align: top;">
                    <strong>INSTRUCTIONS:</strong><br>
                    {!! nl2br(e($jobOrder->instructions ?? '—')) !!}
                </td>
            </tr>
        </table>

        <table class="no-border" style="width: 100%;">
            <tr class="row-border-bottom">
                <td class="text-left" style="vertical-align:top; padding: 2px 0; height: 100px;">
                    <strong>DELIVERY DETAILS:</strong> 
                    {{ $jobOrder->delivery_details ?? $jobOrder->salesOrder->delivery_details ?? '—' }}
                </td>
            </tr>
            <tr>
                <td class="text-left" style="vertical-align:top; padding: 2px 0;">
                    <strong>APPROVED DATE:</strong> 
                    @if ($jobOrder->jo_status === 'approved')
                        {{ optional($jobOrder->approval_date)->format('F d, Y') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
        </table>
    </main>
</body>
</html>
