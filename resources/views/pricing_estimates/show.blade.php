<x-layouts.app title="Price Estimate">
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('pricing-estimates.my')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Price Estimate</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Price Estimate</span>
    </div>


    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    Pricing Estimate #{{ $pricingEstimate->pe_no }}
                </h1>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Created: {{ $pricingEstimate->created_at->format('M d, Y') }}</span>
                    @if($pricingEstimate->updated_at != $pricingEstimate->created_at)
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $pricingEstimate->updated_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
            @php
                $backRoute = auth()->user()->hasAnyRole(['admin', 'super-admin'])
                    ? route('pricing-estimates.index')
                    : route('pricing-estimates.my');
            @endphp
            <a href="{{ $backRoute }}"
               class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>

        {{-- Estimate Info --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">
                <i class="fas fa-info-circle mr-2"></i>Estimate Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $pricingEstimate->customer->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Sales Representative</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ optional($pricingEstimate->salesRep->employee)->full_name ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium">
                        @php $status = $pricingEstimate->pe_status; @endphp
                        @if($status === 'approved')
                            <span class="text-green-600 dark:text-green-400">Approved</span>
                        @elseif($status === 'rejected')
                            <span class="text-red-600 dark:text-red-400">Rejected</span>
                        @else
                            <x-status-badge :status="$status">{{ ucfirst($status) }}</x-status-badge>
                        @endif
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Attention</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $pricingEstimate->attention ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">PR No</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $pricingEstimate->pr_no ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Tax Status</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $pricingEstimate->vat ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400">Terms</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">{{ $pricingEstimate->terms ?? '—' }}</p>
                </div>


                <div>
                    <p class="text-gray-500 dark:text-gray-400">Delivery Date</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $pricingEstimate->delivery_date }}
                    </p>
                </div>

            </div>
        </div>


        {{-- Estimate Items --}}
        @if($pricingEstimate->pe_items->isNotEmpty())
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">
                <i class="fas fa-list-ul mr-2"></i>Items
            </h2>

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
                        @foreach($pricingEstimate->pe_items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->item_name }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $item->item_description }}</div>
                                    @if($item->pe_remarks)
                                        <div class="mt-1 text-xs italic text-gray-500 dark:text-gray-400">
                                            <span class="font-semibold not-italic text-gray-600 dark:text-gray-300">P.E Remarks:</span>
                                            {{ $item->pe_remarks }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-300">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600 dark:text-gray-300">{{ number_format($item->final_unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->quantity * $item->final_unit_price, 2) }}</td>
                            </tr>
                        @endforeach

                        @php
                        $taxStatus = $pricingEstimate->vat ?? '';

                        // Sum of all items
                        $totalSum = $pricingEstimate->pe_items->sum(function ($item) {
                            return $item->final_unit_price * $item->quantity;
                        });

                        // Calculate Total Vatable Sales
                        if ($taxStatus === 'VAT Inclusive') {
                            $totalVatableSales = $totalSum / 1.12; // Extract base price
                        } else {
                            $totalVatableSales = $totalSum; // For other cases (Zero Rated or fallback)
                        }

                        // Calculate VAT and Grand Total
                        if ($taxStatus === 'Zero Rated') {
                            $vat = 0;
                            $grandTotal = $totalSum;
                            $zeroRatedSales = $totalSum;
                            $totalVatableSales = 0; // No vatable sales for Zero Rated
                        } elseif ($taxStatus === 'VAT Inclusive') {
                            $vat = $totalSum - $totalVatableSales;
                            $grandTotal = $totalSum;
                            $zeroRatedSales = 0;
                        } else {
                            $vat = 0;
                            $grandTotal = $totalSum;
                            $zeroRatedSales = 0;
                        }
                    @endphp

                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm text-gray-700 dark:text-gray-200">Total Vatable Sales</td>
                        <td class="px-6 py-4 text-right font-medium text-sm text-gray-900 dark:text-gray-100">{{ number_format($totalVatableSales, 2) }}</td>
                    </tr>

                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm text-gray-700 dark:text-gray-200">Zero Rated Sales</td>
                        <td class="px-6 py-4 text-right font-medium text-sm text-gray-900 dark:text-gray-100">{{ number_format($zeroRatedSales, 2) }}</td>
                    </tr>

                    @if ($vat > 0)
                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm text-gray-700 dark:text-gray-200">VAT 12%</td>
                        <td class="px-6 py-4 text-right font-medium text-sm text-gray-900 dark:text-gray-100">{{ number_format($vat, 2) }}</td>
                    </tr>
                    @endif

                    <tr class="bg-gray-100 dark:bg-gray-600">
                        <td colspan="4" class="px-6 py-4 text-right font-bold text-sm text-gray-800 dark:text-gray-100">Total Sales Amount</td>
                        <td class="px-6 py-4 text-right font-bold text-sm text-gray-900 dark:text-gray-100">{{ number_format($grandTotal, 2) }}</td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Remarks --}}
        @if($pricingEstimate->remarks)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">
                    <i class="fas fa-comment-dots mr-2"></i>Remarks
                </h2>
                <p class="text-gray-600 dark:text-gray-300">{{ $pricingEstimate->remarks }}</p>
            </div>
        @endif
    </div>
</x-layouts.app>
