@props(['quotation'])

@php
    $editing = $quotation->exists;
@endphp
<style>
    [x-cloak] { display: none !important; }
</style>
<div x-data="discountModal()">


    <form
        {{ $attributes->merge(['class' => 'space-y-6']) }}
        method="POST"
        action="{{ $editing ? route('quotations.update', $quotation) : route('quotations.store') }}"
    >
        @csrf
        @if($editing) @method('PUT') @endif

        @if(!$pe_no)
        <div class="grid grid-cols-1 gap-6 w-full">
            <p class="text-center">No PE found.</p>
        </div>
        @else
        <div class="grid grid-cols-1 gap-6 w-full">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                <h2 class="text-xl font-semibold text-black-100">Customer Details</h2>
                    <span class="inline-flex flex-wrap gap-1">
                    {{-- Pricing Estimate --}}
                    <a href="{{ route('pricing-estimates.show', $pricing_estimate->id) }}" target="_blank"
                    class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-blue-800">
                        P.E NO: #{{ $pricing_estimate->pe_no }}
                    </a>

                    {{-- Approved Quotation --}}
                    @if($pricing_estimate->approvedQuotation)
                        <a href="{{ route('quotations.edit', $pricing_estimate->approvedQuotation->id) }}" target="_blank"
                        class="bg-green-900 text-green-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-green-800">
                            Quotation NO: #{{ $pricing_estimate->approvedQuotation->quote_no }}
                        </a>

                        {{-- Sales Order --}}
                        @if($pricing_estimate->approvedQuotation->salesOrder)
                            <a href="{{ route('sales-orders.show', $pricing_estimate->approvedQuotation->salesOrder->id) }}" target="_blank"
                            class="bg-yellow-900 text-yellow-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-yellow-800">
                                S.O NO: #{{ $pricing_estimate->approvedQuotation->salesOrder->so_no }}
                            </a>

                            {{-- Job Order --}}
                            @if($pricing_estimate->approvedQuotation->salesOrder->jobOrder)
                                <a href="{{ route('job-orders.show', $pricing_estimate->approvedQuotation->salesOrder->jobOrder->id) }}" target="_blank"
                                class="bg-red-900 text-red-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-red-800">
                                    J.O NO: #{{ $pricing_estimate->approvedQuotation->salesOrder->jobOrder->jo_no }}
                                </a>
                            @endif
                        @endif
                    @endif
                </span>
            </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Name</p>
                    <p class="text-black-100">{{ $pricing_estimate->customer->name }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">P.R No:</p>
                    <p class="text-black-100 font-mono">#{{ $pricing_estimate->pr_no }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Attention</p>
                    <p class="text-black-100">{{ $pricing_estimate->attention }}</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Terms</p>
                    <p class="text-black-100">{{ $pricing_estimate->terms }}</p>
                    </div>
                </div>
                </div>
            </div>
            
        </div>
        
        <div class="grid grid-cols-1 gap-6 w-full mb-6">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-black-100 mb-4 border-b border-gray-600">Quote Remarks</h2>
                <p class="text-black-100 whitespace-pre-line">
                    {{ $quotation->quote_remarks ?? '—' }}
                </p>
            </div>
        </div>

    <!-- DISCOUNT MODAL -->
        {{-- <div x-show="openModal" 
        x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>

        <!-- Modal Container -->
        <div x-show="openModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md">
                <!-- Modal Header -->
                <div class="flex justify-between items-center px-6 py-4 border-b dark:border-gray-700">
                    <h3 class="text-xl font-semibold dark:text-gray-100">Add Discount</h3>
                    <button type="button" @click="closeModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Discount Type</label>
                        <select x-model="discountType" 
                                class="w-full px-3 py-2 border dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount</option>
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            <span x-text="discountType === 'percentage' ? 'Discount Percentage' : 'Discount Amount'"></span>
                        </label>
                        <input type="number" x-model="discountAmount" step="0.01" min="0"
                            class="w-full px-3 py-2 border dark:border-gray-600 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700">
                    </div>
                    
                    <div class="bg-gray-100 dark:bg-gray-700 p-3 rounded mb-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            Original Price: {{ $pricing_estimate->currency->symbol }}<span x-text="formatNumber(currentItem ? currentItem.unit_price : 0)" class="font-medium"></span>
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            Discounted Price: {{ $pricing_estimate->currency->symbol }}<span x-text="formatNumber(calculateDiscountedPrice())" class="font-medium"></span>
                        </p>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t dark:border-gray-700 flex justify-end space-x-3">
                    <button type="button" @click="closeModal()" 
                            class="px-4 py-2 border rounded hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-gray-200">
                        Cancel
                    </button>
                    <button type="button" @click="saveDiscount()" 
                            :disabled="isLoading"
                            :class="{'opacity-50 cursor-not-allowed': isLoading}"
                            class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <span x-show="!isLoading">Apply Discount</span>
                        <span x-show="isLoading">Saving...</span>
                    </button>
                </div>
            </div>
        </div> --}}

        <div x-data class="quotation-table">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <th class="px-2 py-4 text-center align-middle">
                                <input type="checkbox" 
                                    x-ref="selectAll"
                                    @click="$event.target.checked 
                                        ? $store.quotationSelection.selected = @json(
                                            $pe_items->filter(function($item) use ($pe_items) {
                                                return $pe_items->where('parent_item_id', $item->id)->isEmpty() || $item->parent_item_id !== null;
                                            })->pluck('id')->values()
                                        ) 
                                        : $store.quotationSelection.selected = [];
                                        
                                        // force all checkboxes to reflect visually
                                        document.querySelectorAll('input[name=\'selected_items[]\']').forEach(el => el.checked = $event.target.checked);"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mx-auto">
                                    </th>
                            <th class="px-2 py-4 w-16 whitespace-nowrap text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400 uppercase tracking-wider">
                                Item Name
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400 uppercase tracking-wider">
                                Unit Price
                            </th>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php $index = 1; @endphp
                            @foreach ($pe_items as $pe_item)
                                @if ($pe_item->is_offer)
                                    @continue
                                @endif

                                @php
                                    $offers = $pe_items->where('parent_item_id', $pe_item->id);
                                    $hasOffers = $offers->isNotEmpty();
                                @endphp

                                {{-- Parent Row --}}
                                <tr class="font-semibold text-sm text-gray-800 dark:text-gray-100">
                                    <td class="px-2 py-4 text-center align-middle align-top">
                                        @if (!$hasOffers)
                                       <input type="checkbox"
                                            name="selected_items[]"
                                            value="{{ $pe_item->id }}"
                                            @change="$event.target.checked 
                                                ? $store.quotationSelection.selected.push({{ $pe_item->id }}) 
                                                : $store.quotationSelection.selected = $store.quotationSelection.selected.filter(id => id !== {{ $pe_item->id }})"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded">

                                        @endif
                                    </td>
                                    <td class="px-4 py-2">
                                        {{ $index++ }}
                                    </td>
                                    <td class="px-4 py-2">
                                        <div>{{ $pe_item->item_name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $pe_item->item_description }}</div>
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $hasOffers ? '—' : $pe_item->quantity }}</td>
                                    <td class="px-4 py-2 text-sm text-center">
                                        @if (!$hasOffers)
                                            <div class="flex flex-col items-center">
                                                <!-- Final / Discounted Price -->
                                                <span class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $pricing_estimate->currency->symbol }}{{ number_format($pe_item->final_unit_price ?? $pe_item->unit_price, 2) }}
                                                </span>

                                                @if ($pe_item->discount_amount > 0)
                                                    <div class="flex flex-col text-xs text-green-600 dark:text-green-400">
                                                        <!-- Original Price with Strikethrough -->
                                                        <span class="line-through text-gray-500 dark:text-gray-400">
                                                            {{ $pricing_estimate->currency->symbol }}{{ number_format($pe_item->unit_price, 2) }}
                                                        </span>

                                                        <!-- Discount Label -->
                                                        <span>
                                                            ({{ $pe_item->discount_type === 'percentage'
                                                                ? $pe_item->discount_amount . '% off'
                                                                : ($pricingEstimate->currency->symbol ?? '') . number_format($pe_item->discount_amount, 2) . ' off'
                                                            }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400">—</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Offer Header --}}
                                @if ($hasOffers)
                                    <tr class="bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-300 uppercase tracking-wide">
                                        {{-- <td></td>
                                        <td></td> --}}
                                        <td colspan="5" class="px-4 py-2 text-center">Offer List</td>
                                    </tr>

                                    {{-- Offer Rows --}}
                                    @foreach ($offers as $offer)
                                        <tr class="bg-blue-50 dark:bg-blue-900/30 text-sm text-gray-800 dark:text-gray-100">
                                            <td class="px-2 py-4 text-center align-middle">
                                                <input type="checkbox"
                                                    name="selected_items[]"
                                                    value="{{ $offer->id }}"
                                                    @change="$event.target.checked 
                                                        ? $store.quotationSelection.selected.push({{ $offer->id }}) 
                                                        : $store.quotationSelection.selected = $store.quotationSelection.selected.filter(id => id !== {{ $offer->id }})"
                                                    class="h-4 w-4 text-blue-600 border-gray-300 rounded">

                                            </td>
                                            <td class="px-4 py-2">
                                            </td>
                                            <td class="px-4 py-2">
                                                <div>{{ $offer->item_name }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $offer->item_description }}</div>
                                            </td>
                                            <td class="px-4 py-2 text-center">{{ $offer->quantity }}</td>
                                            <td class="px-4 py-2 text-sm text-center">
                                                <!-- Discounted Price -->
                                                <span class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $pricing_estimate->currency->symbol }}{{ number_format($offer->final_unit_price ?? $offer->unit_price, 2) }}
                                                </span>

                                                @if ($offer->discount_amount > 0)
                                                    <div class="flex flex-col text-xs text-green-600 dark:text-green-400">
                                                        <!-- Original Price with Strikethrough -->
                                                        <span class="line-through text-gray-500 dark:text-gray-400">
                                                            {{ $pricing_estimate->currency->symbol }} {{ number_format($offer->unit_price, 2) }}
                                                        </span>

                                                        <!-- Discount Label -->
                                                        <span>
                                                            ({{ $offer->discount_type === 'percentage'
                                                                ? $offer->discount_amount . '% off'
                                                                : ($pricingEstimate->currency->symbol ?? ''). number_format($offer->discount_amount, 2) . ' off'
                                                            }})
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        @if($quotation->quote_status === 'pending')
        <div 
            x-data="quotationApproval"
            data-approve-url="{{ route('quotations.approve', $quotation) }}"
            data-reject-url="{{ route('quotations.reject', $quotation) }}"
            class="flex justify-center space-x-4 mb-4"
        >
            <!-- Reject Button -->
            <button type="button"
                    @click="openModal('reject')"
                    class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none dark:bg-red-700 dark:hover:bg-red-800">
                <i class="fa fa-times mr-1"></i> Disapprove
            </button>

            <!-- Approve Button -->
            <button type="button"
                    @click="openModal('approve')"
                    class="px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none dark:bg-green-700 dark:hover:bg-green-800">
                <i class="fa fa-check mr-1"></i> Approve Quotation
            </button>

            <a href="{{ route('reports.quotations',$pe_no) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium
                    text-white bg-blue-600 hover:bg-blue-700
                    border border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <i class="fas fa-print"></i>
                Print
            </a>

            <!-- Modal -->
            <div 
                x-show="showDialog" 
                x-transition.opacity 
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            >
                <div @click.outside="showDialog = false"
                    class="w-full max-w-md bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" x-text="modalTitle"></h3>

                    <p class="mt-2 text-gray-600 dark:text-gray-300" x-text="modalMessage"></p>

                    <div x-show="action === 'reject'" class="mt-4">
                        <textarea x-model="revisionReason"
                                placeholder="Enter reason for disapproval..."
                                class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 dark:text-white">
                        </textarea>
                        <p x-show="error" class="text-red-500 text-sm mt-1" x-text="error"></p>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="showDialog = false"
                                class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700">
                            Cancel
                        </button>

                        <button type="button" @click="confirmAction"
                                {{-- :disabled="(action === 'reject' && revisionReason.trim().length < 10) || isLoading" --}}
                                :disabled="isLoading"
                                :class="{
                                    'bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800': action === 'approve',
                                    'bg-red-600 hover:bg-red-700 dark:bg-red-700 dark:hover:bg-red-800': action === 'reject',
                                    'opacity-50 cursor-not-allowed': isLoading
                                }"
                                class="px-4 py-2 rounded-md text-white flex items-center">
                            <span x-text="action === 'approve' ? 'Yes' : 'Ok'"></span>
                            <svg x-show="isLoading" class="animate-spin -mr-1 ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @endif

    </form>
    @if($quotation->quote_status == 'approved')
        <div class="flex justify-end space-x-4">
            <a href="{{ route('quotations.index') }}"
            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                    text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                    border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                    focus:outline-none focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-chevron-left mr-1"></i> Back to List
            </a>
            <a href="{{ route('reports.quotations',$pe_no) }}" target="_blank"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium
                    text-white bg-blue-600 hover:bg-blue-700
                    border border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <i class="fas fa-print"></i>
                Print
            </a>
            <form 
                x-data 
                @submit.prevent="Swal.fire({
                    title: 'Export Selected Items?',
                    text: 'This will export the selected quotation items to a Sales Order.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, export',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#2563eb', // Tailwind blue-600
                    cancelButtonColor: '#d33'
                }).then(result => {
                    if (result.isConfirmed) {
                        $el.submit();
                    }
                })"
                action="{{ route('quotations.export-to-sales-order', $quotation) }}"
                method="POST"
                x-show="$store.quotationSelection.selected.length > 0"
                x-cloak
            >
                @csrf
                <input type="hidden" name="selected_items" :value="JSON.stringify($store.quotationSelection.selected)">
                <button type="submit" 
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-paper-plane mr-2"></i> Export to Sales Order
                </button>
            </form>


        </div>
    @endif
    @if($quotation->quote_status == 'rejected')
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-4 rounded relative mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-200">
            <strong class="font-bold">Quotation Rejected!</strong>
            <p class="mt-1">Please update the Price Estimate accordingly.</p>

            @if($quotation->revision_reason)
                <p class="mt-2 text-sm italic text-red-700 dark:text-red-300">
                    Reason: "{{ $quotation->revision_reason }}"
                </p>
            @endif

            <div class="mt-4">
                <a href="{{ route('pricing-estimates.price-entry', $quotation->pricing_estimate->id) }}"
                class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md dark:bg-red-700 dark:hover:bg-red-800">
                    <i class="fas fa-chevron-left mr-2"></i> Back to Price Entry
                </a>
            </div>
        </div>
    @endif


</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('quotationSelection', {
            selected: []
        })
        Alpine.data('quotationApproval', () => ({
            showDialog: false,
            action: null,
            revisionReason: '',
            isLoading: false,
            error: null,

            approveUrl: '',
            rejectUrl: '',

            get modalTitle() {
                return this.action === 'approve' ? 'Approve Quotation?' : 'Disapprove Quotation?';
            },

            get modalMessage() {
                return this.action === 'approve'
                    ? 'Are you sure you want to approve this quotation?'
                    : 'Please provide a reason for disapproval (Optional):';
            },

            init() {
                this.approveUrl = this.$el.dataset.approveUrl;
                this.rejectUrl = this.$el.dataset.rejectUrl;
            },

            openModal(actionType) {
                this.action = actionType;
                this.revisionReason = '';
                this.error = null;
                this.showDialog = true;
            },

            async confirmAction() {
                this.isLoading = true;
                this.error = null;

                const url = this.action === 'approve' ? this.approveUrl : this.rejectUrl;
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                
                // if (this.action === 'reject') {
                //     formData.append('revision_reason', this.revisionReason.trim());
                // }
                formData.append('revision_reason', this.revisionReason?.trim() || '');
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Action failed');
                    }

                    window.location.reload();
                } catch (error) {
                    this.error = error.message || 'Action failed.';
                    console.error(error);
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });

function discountModal() {
    return {
        openModal: false,
        currentItem: null,
        discountType: 'percentage',
        discountAmount: 0,
        isLoading: false,

        init() {
            window.addEventListener('open-discount-modal', (e) => {
                this.openDiscountModal(e.detail);
            });
        },

        openDiscountModal(item) {
            this.currentItem = item;
            this.discountType = item.discount_type || 'percentage';
            this.discountAmount = item.discount_amount || 0;
            this.openModal = true;
        },

        closeModal() {
            this.openModal = false;
            this.currentItem = null;
        },

        formatNumber(number) {
            return parseFloat(number || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        calculateDiscountedPrice() {
            if (!this.currentItem) return 0;
            const price = parseFloat(this.currentItem.unit_price || 0);
            const amount = parseFloat(this.discountAmount || 0);
            return this.discountType === 'percentage'
                ? price * (1 - amount / 100)
                : price - amount;
        },

        async saveDiscount() {
            if (!this.currentItem) return;
            this.isLoading = true;

            try {
                const response = await fetch(`/pricing-estimate-items/${this.currentItem.id}/apply-discount`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        discount_type: this.discountType,
                        discount_value: this.discountAmount
                    })
                });

                if (!response.ok) throw new Error('Failed to save discount');

                this.closeModal();
                this.$dispatch('discount-updated');
                window.location.reload(); // Optional: remove if you update via JS instead
            } catch (error) {
                console.error(error);
                alert('Failed to save discount');
            } finally {
                this.isLoading = false;
            }
        }
    }
}

// 🔄 Global function to open modal
window.openItemDiscountModal = function (item) {
    window.dispatchEvent(new CustomEvent('open-discount-modal', { detail: item }));
};

</script>
