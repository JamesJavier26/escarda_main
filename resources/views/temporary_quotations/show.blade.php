<x-layouts.app title="Temporary Quotation">
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('temporary_quotations.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Temporary Quotations</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Temporary Quotation</span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    Temporary Quotation #{{ $temporaryQuotation->id }}
                </h1>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Created: {{ $temporaryQuotation->created_at->format('M d, Y') }}</span>
                    @if($temporaryQuotation->updated_at != $temporaryQuotation->created_at)
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $temporaryQuotation->updated_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>

            @php
                $backRoute = auth()->user()->hasAnyRole(['admin', 'super-admin'])
                    ? route('temporary_quotations.index')
                    : route('temporary_quotations.index');
            @endphp
            <a href="{{ $backRoute }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>

        {{-- Quotation Info --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">
                <i class="fas fa-info-circle mr-2"></i>Quotation Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $temporaryQuotation->customer->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Sales Representative</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ optional($temporaryQuotation->salesRep->employee)->full_name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Attention</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $temporaryQuotation->attention ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">PR No</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $temporaryQuotation->pr_no ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Terms</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $temporaryQuotation->terms ?? '—' }}</p>
                </div>
            </div>
        </div>

{{-- Items --}}
@if($temporaryQuotation->items && $temporaryQuotation->items->isNotEmpty())
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <i class="fas fa-list-ul text-blue-500"></i>
            Items
        </h3>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Item</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Qty</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($temporaryQuotation->items as $index => $item)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->name ?? $item->item_name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->description ?? $item->item_description }}</div>
                        @if(!empty($item->remarks) || !empty($item->pe_remarks))
                        <div class="mt-1 text-xs italic text-gray-500 dark:text-gray-400">
                            <span class="font-semibold not-italic text-gray-600 dark:text-gray-300">Remarks:</span>
                            {{ $item->remarks ?? $item->pe_remarks }}
                        </div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                    <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-300">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-gray-100">{{ number_format(($item->quantity ?? 0) * ($item->unit_price ?? 0), 2) }}</td>
                </tr>
                @endforeach

                @php
                    // Calculate subtotal: sum of item unit_price * quantity
                    $subtotal = $temporaryQuotation->items->sum(function ($item) {
                        return $item->unit_price * $item->quantity;
                    });

                    $taxStatus = $temporaryQuotation->customer->tax_status ?? 'Tax Exempt';

                    if ($taxStatus === 'Tax Exempt') {
                        $vat = 0;
                    } elseif ($taxStatus === 'VAT Inclusive') {
                        $vat = $subtotal - ($subtotal / 1.12);
                    } elseif ($taxStatus === 'VAT Exclusive') {
                        $vat = $subtotal * 0.12;
                    } else {
                        $vat = 0; // fallback if unexpected tax_status
                    }

                    $grandTotal = ($taxStatus === 'VAT Exclusive') ? $subtotal + $vat : $subtotal;
                @endphp

                {{-- Totals --}}
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm text-gray-700 dark:text-gray-200">Subtotal</td>
                    <td class="px-6 py-4 text-right font-medium text-sm text-gray-900 dark:text-gray-100">{{ number_format($subtotal, 2) }}</td>
                </tr>

                @if ($vat > 0)
                <tr class="bg-gray-50 dark:bg-gray-700">
                    <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm text-gray-700 dark:text-gray-200">{{ $taxStatus }} 12%</td>
                    <td class="px-6 py-4 text-right font-medium text-sm text-gray-900 dark:text-gray-100">{{ number_format($vat, 2) }}</td>
                </tr>
                @endif

                <tr class="bg-gray-100 dark:bg-gray-600">
                    <td colspan="4" class="px-6 py-4 text-right font-bold text-sm text-gray-800 dark:text-gray-100">Total</td>
                    <td class="px-6 py-4 text-right font-bold text-sm text-gray-800 dark:text-gray-100">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endif


        {{-- Remarks --}}
        @if($temporaryQuotation->remarks)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-comment-dots mr-2"></i>Remarks
                </h2>
                <p class="text-gray-600 dark:text-gray-300">{{ $temporaryQuotation->remarks }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>
