<x-layouts.app>
    <!-- Breadcrumb Navigation -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('job-orders.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Job Orders</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Job Order</span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Header and Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    Job Order #{{ $jobOrder->jo_no }}
                </h1>
                <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Created: {{ $jobOrder->created_at->format('M d, Y') }}</span>
                    @if($jobOrder->approval_date)
                        <span class="mx-2">&bull;</span>
                        <span>Approval: {{ $jobOrder->approval_date->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                {{-- @if($jobOrder->jo_status == 'delivered') --}}
                <a href="{{ route('job-orders.edit', $jobOrder) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i> Edit Job Order
                </a>
                {{-- @endif --}}
                <a href="{{ route('job-orders.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        <!-- Job Order Info -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-lg font-semibold text-gray-700 dark:text-gray-300">
            Job Order Information
        </h2>

        <span class="inline-flex flex-wrap gap-1">
            {{-- Pricing Estimate --}}
            @if($jobOrder->salesOrder && $jobOrder->salesOrder->quotation && $jobOrder->salesOrder->quotation->pricing_estimate)
                <a href="{{ route('pricing-estimates.show', $jobOrder->salesOrder->quotation->pricing_estimate->id) }}" target="_blank"
                   class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-blue-800">
                    P.E NO: #{{ $jobOrder->salesOrder->quotation->pricing_estimate->pe_no }}
                </a>
            @endif

            {{-- Quotation --}}
            @if($jobOrder->salesOrder && $jobOrder->salesOrder->quotation)
                <a href="{{ route('quotations.edit', $jobOrder->salesOrder->quotation->id) }}" target="_blank"
                   class="bg-green-900 text-green-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-green-800">
                    Quotation NO: #{{ $jobOrder->salesOrder->quotation->quote_no }}
                </a>
            @endif

            {{-- Sales Order --}}
            @if($jobOrder->salesOrder)
                <a href="{{ route('sales-orders.show', $jobOrder->salesOrder->id) }}" target="_blank"
                   class="bg-yellow-900 text-yellow-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-yellow-800">
                    S.O NO: #{{ $jobOrder->salesOrder->so_no }}
                </a>
            @endif

            {{-- Job Order --}}
            <a href="{{ route('job-orders.show', $jobOrder->id) }}" target="_blank"
               class="bg-red-900 text-red-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-red-800">
                J.O NO: #{{ $jobOrder->jo_no }}
            </a>
        </span>
    </div>

    <hr class="border-t border-gray-300 dark:border-gray-600 mb-4">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Sales Order</p>
            <p class="font-medium">{{ optional($jobOrder->salesOrder)->so_no ?? 'N/A' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Due Date</p>
            <p class="font-medium">{{ $jobOrder->duedate ? $jobOrder->duedate->format('M d, Y') : 'N/A' }}</p>
        </div>
        <div>
            @php
                $status = $jobOrder->jo_status;
            @endphp
            <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
            <x-status-badge :status="$jobOrder->jo_status">{{ ucfirst($status) }}</x-status-badge>
        </div>
    </div>
</div>


        <!-- Instructions and Delivery -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Instructions & Delivery</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Instructions</p>
                    <p class="font-medium whitespace-pre-line">{{ $jobOrder->instructions ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Delivery Details</p>
                    <p class="font-medium whitespace-pre-line">{{ $jobOrder->delivery_details ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Remarks -->
        @if($jobOrder->remarks)
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Remarks</h2>
            <p class="text-gray-600 dark:text-gray-300">{{ $jobOrder->remarks }}</p>
        </div>
        @endif

        <!-- Job Items Table -->
        @if($jobOrder->jo_items->count())
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Job Order Items</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Item</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Material</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Hardness</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Thickness</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($jobOrder->jo_items as $item)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-medium">{{ $item->item->name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $item->item->description ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->material->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->hardness }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->thickness }} {{ $item->thickness_unit }}</td>
                            <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
