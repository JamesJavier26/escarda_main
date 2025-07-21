<x-layouts.app title="Price Estimate">
    @php
        $items = $pricingEstimate->pe_items->map(function ($item) {
            return array_merge($item->toArray(), [
                'final_unit_price' => $item->final_unit_price,
            ]);
        });
    @endphp
<div x-data="priceTable()">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-gray-400" />
        <a href="{{ route('pricing-estimates.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Price Estimate</a>
        <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-gray-400" />
        <span class="text-gray-500 dark:text-gray-400">Price Entry</span>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Price Entry</h1>
        <p class="text-gray-600 dark:text-gray-400">Review and update pricing for items.</p>
        <div class="text-sm text-gray-300 mt-1">
            {{-- Sales Rep: <span class="font-medium text-white">{{ $pricingEstimate->salesRep->employee->full_name ?? 'N/A' }}</span> --}}
        </div>

    </div>

{{-- Customer & Estimate Info --}}
<div class="grid grid-cols-1 gap-6 w-full mb-6">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100">Customer & Estimate Details</h2>
            <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded">
                P.E NO: #{{ $pricingEstimate->pe_no }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Customer Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">Customer Name</p>
                    <p class="text-gray-100">{{ $pricingEstimate->customer->name ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Fax No.</p>
                    <p class="text-gray-100">{{ $pricingEstimate->customer->telefax ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Telephone No.</p>
                    <p class="text-gray-100">{{ $pricingEstimate->customer->telno ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Email</p>
                    <p class="text-gray-100">{{ $pricingEstimate->customer->email ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Address</p>
                    <p class="text-gray-100">{{ $pricingEstimate->customer->address ?? '--' }}</p>
                </div>
            </div>

            {{-- Estimate Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">PR No.</p>
                    <p class="text-gray-100">{{ $pricingEstimate->pr_no ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Delivery Date</p>
                    <p class="text-gray-100">
                        {{-- {{ \Carbon\Carbon::parse($pricingEstimate->delivery_date)->format('F j, Y') }} --}}
                        {{ $pricingEstimate->delivery_date }}
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Terms</p>
                    <p class="text-gray-100">{{ $pricingEstimate->terms ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Attention</p>
                    <p class="text-gray-100">{{ $pricingEstimate->attention ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Currency</p>
                    <p class="text-gray-100">{{ $pricingEstimate->currency->code ?? 'PHP' }}</p>
                </div>
            </div>

            {{-- Status / Sales Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">Sales Representative</p>
                    <p class="text-gray-100">{{ $pricingEstimate->salesRep->employee->full_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Estimate Status</p>
                             @php $status = $pricingEstimate->pe_status; @endphp
                        @if($status === 'approved')
                            <span class="text-green-600 dark:text-green-400">Approved</span>
                        @elseif($status === 'rejected')
                            <span class="text-red-600 dark:text-red-400">Rejected</span>
                        @else
                            <x-status-badge :status="$status">{{ ucfirst($status) }}</x-status-badge>
                        @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Remarks</p>
                    <p class="text-gray-100 whitespace-pre-line">{{ $pricingEstimate->remarks ?? '--' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>


    {{-- Items Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                <i class="fas fa-list-ul text-blue-500"></i> Items
            </h3>
        </div>

        <div class="p-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">#</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Item</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Qty</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Unit Price</th>
                        @if (
                            $pricingEstimate->pe_status === 'pending' &&
                            !$pricingEstimate->quotations->contains(function ($quote) {
                                return $quote->quote_status === 'approved' && $quote->is_final;
                            })
                        )
                            <th class="px-4 py-2 text-center text-sm font-medium text-gray-600 dark:text-gray-300">Actions</th>
                        @endif

                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <template x-for="(item, idx) in displayItems()" :key="item.id ?? `header-${idx}`">
                        <tr
                            :class="{
                                'bg-blue-50 dark:bg-blue-900/20': item.__type === 'offer',
                                'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 text-sm font-semibold': item.__type === 'offer-header'
                            }"
                        >
                            <!-- Number column -->
                            <td class="px-4 py-2 text-sm" x-text="item.__type === 'parent' ? getMainItemIndex(idx) : ''"></td>

                            <!-- Name + Description -->
                            <td class="px-4 py-2" :colspan="item.__type === 'offer-header' ? 1 : 1">
                                <template x-if="item.__type === 'parent'">
                                    <div>
                                        <div class="font-semibold" x-text="item.item_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.item_desc"></div>
                                        <template x-if="item.item_description">
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-1" x-text="item.item_description"></div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="item.__type === 'offer-header'">
                                    <div class="uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Offer List</div>
                                </template>

                                <template x-if="item.__type === 'offer'">
                                    <div>
                                        <div class="font-semibold" x-text="item.item_name"></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400" x-text="item.item_desc"></div>
                                        <template x-if="item.item_description">
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-1" x-text="item.item_description"></div>
                                        </template>
                                    </div>
                                </template>
                            </td>

                            <!-- Quantity -->
                            <td class="px-4 py-2 text-sm" x-text="item.__type === 'offer' || item.__type === 'parent' ? item.quantity : ''"></td>

                            <!-- Price -->
                            <td class="px-4 py-2 text-sm">
                                <template x-if="item.__type === 'offer' || item.__type === 'parent'">
                                    <div class="flex flex-col">
                                        <span>₱<span x-text="formatCurrency(item.final_unit_price ?? item.unit_price)"></span></span>
                                        <template x-if="item.discount_amount > 0">
                                            <span class="text-xs text-green-500 dark:text-green-400">
                                                (<span x-text="item.discount_type === 'percentage' ? 
                                                    `${item.discount_amount}% off` : 
                                                    `₱${formatCurrency(item.discount_amount)} off`"></span>)
                                            </span>
                                        </template>
                                    </div>
                                </template>
                            </td>

                            <!-- Actions -->
                            @if (
                                $pricingEstimate->pe_status === 'pending' &&
                                !$pricingEstimate->quotations->contains(fn ($q) => $q->quote_status === 'approved' && $q->is_final)
                            )
                            <td class="px-4 py-3 text-center space-x-2">
                                <template x-if="item.__type === 'offer'">
                                    <button type="button" @click="openPriceModal(item)"
                                        class="text-blue-500 hover:text-blue-700 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-gray-700"
                                        title="Add Price">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </template>

                                <template x-if="item.__type === 'parent' && !hasOfferFor(item.id)">
                                    <button type="button" @click="openPriceModal(item)"
                                        class="text-blue-500 hover:text-blue-700 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-gray-700"
                                        title="Add Price">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </template>

                                <template x-if="item.__type === 'parent'">
                                    <button type="button" @click="openOfferModal(item)"
                                        class="text-green-500 hover:text-green-700 transition-colors p-1 rounded hover:bg-green-50 dark:hover:bg-gray-700"
                                        title="Create Offer">
                                        <i class="fas fa-tag"></i>
                                    </button>
                                </template>
                            </td>
                            @endif
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    {{-- P.E Remarks & Attachments --}}
    <div class="grid grid-cols-1 gap-6 w-full mb-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-100 mb-4 border-b border-gray-600 pb-2">P.E. Remarks & Attachments</h2>

            {{-- Remarks --}}
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-400">Remarks</p>
                <p class="text-gray-100 whitespace-pre-line">
                    {{ $pricingEstimate->remarks ?? '—' }}
                </p>
            </div>

            {{-- Attachments --}}

            @if ($pricingEstimate->attachments && count($pricingEstimate->attachments))
                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Attachments</h4>
                    <div class="grid gap-3">
                        @foreach ($pricingEstimate->attachments as $file)
                            @php
                                $meta = is_array($file->metadata) ? $file->metadata : json_decode($file->metadata, true);
                                $fileUrl = asset("storage/{$meta['path']}");
                                $fileType = explode('/', $meta['mime_type'])[0];
                                $fileSize = \App\Helpers\Helper::formatBytes($meta['size']);
                                $fileExtension = pathinfo($meta['original_name'], PATHINFO_EXTENSION);
                            @endphp

                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-sm transition-all">
                                <div class="flex items-center space-x-3 min-w-0">
                                    {{-- File Type Icon --}}
                                    <div class="flex-shrink-0">
                                        @switch($fileType)
                                            @case('image')
                                                <div class="w-10 h-10 rounded-md bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                    <i class="fas fa-image text-blue-500 dark:text-blue-400"></i>
                                                </div>
                                                @break
                                            @case('application')
                                                <div class="w-10 h-10 rounded-md bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                                    <i class="fas fa-file-alt text-purple-500 dark:text-purple-400"></i>
                                                </div>
                                                @break
                                            @case('text')
                                                <div class="w-10 h-10 rounded-md bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                    <i class="fas fa-file-text text-green-500 dark:text-green-400"></i>
                                                </div>
                                                @break
                                            @default
                                                <div class="w-10 h-10 rounded-md bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                    <i class="fas fa-file text-gray-500 dark:text-gray-400"></i>
                                                </div>
                                        @endswitch
                                    </div>

                                    {{-- File Info --}}
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $meta['original_name'] }}">
                                            {{ $file->label ?: $meta['original_name'] }}
                                        </p>
                                        <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span>{{ strtoupper($fileExtension) }}</span>
                                            <span>•</span>
                                            <span>{{ $fileSize }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex space-x-2">
                                    {{-- Preview Button (opens modal) --}}
                                    <button 
                                        @click="window.open('{{ $fileUrl }}', '_blank')" 
                                        class="p-2 text-gray-500 hover:text-blue-500 dark:hover:text-blue-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        title="Preview">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    {{-- Download Button --}}
                                    <a href="{{ $fileUrl }}" download
                                    class="p-2 text-gray-500 hover:text-green-500 dark:hover:text-green-400 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                    title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-4">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Remarks</h2>
            <textarea name="quote_remarks" rows="4"
                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
            >Quotation is valid for 30 days</textarea>

        </div>
    </div>
    @if($pricingEstimate->pe_status === 'pending')
    <div 
        x-data="PEApproval"
        data-approve-url="{{ route('quotations.generate', $pricingEstimate->id) }}"
        data-reject-url="{{ route('pricing-estimates.reject', $pricingEstimate) }}"
        class="flex justify-center space-x-4 mb-4"
    >
        <!-- Reject Button -->
        <button type="button"
                @click="openModal('reject')"
                class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none dark:bg-red-700 dark:hover:bg-red-800">
            <i class="fa fa-times mr-1"></i> Disapprove
        </button>

        <!-- Approve Button -->
        {{-- <button type="button"
                @click="openModal('approve')"
                class="px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none dark:bg-green-700 dark:hover:bg-green-800">
            <i class="fa fa-check mr-1"></i> Approve and Create Quotation
        </button> --}}
        <div x-data>
            <button
                @click="
                    const remarks = document.querySelector('textarea[name=\'quote_remarks\']').value;
                    Swal.fire({
                        title: 'Approve Quotation?',
                        html: `Are you sure you want to approve and create the quotation?<br><br>
                            <strong>Remarks:</strong><br>
                            <div class='p-2 bg-gray-100 rounded mt-2'>${remarks || 'No remarks provided'}</div>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, approve it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const url = new URL('{{ route('quotations.generate', $pricingEstimate->id) }}');
                            url.searchParams.append('remarks', remarks);
                            window.location.href = url.toString();
                        }
                    })
                "
                class="px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none">
                <i class="fa fa-check mr-1"></i> Approve and Create Quotation
            </button>
        </div>

        <!-- Modal -->
        <div 
            x-show="showDialog" 
            x-transition.opacity 
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


    {{-- Generate Quotation --}}
    {{-- <div class="flex justify-end mt-6">
        <a href="{{ route('quotations.generate', $pricingEstimate->id) }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
             <i class="fas fa-eye mr-2"></i> View Quotation
        </a>
    </div> --}}

    <!-- Price Modification Modal -->
    <x-modal name="price-modification" maxWidth="6xl">
        <div x-data="priceModification()" class="p-6">
            <h2 class="text-lg font-semibold mb-4">Price Modification</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column - Price Factors -->
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-md font-medium mb-3 text-gray-700 dark:text-gray-300">Price Factors</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">R</span>
                                </label>
                                <input type="number" step="0.0001" x-model="factorRates.R" @input="calculateUnitPrice"
                                    class="w-24 px-3 py-2 border rounded-md text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">E</span>
                                </label>
                                <input type="number" step="0.0001" x-model="factorRates.E" @input="calculateUnitPrice"
                                    class="w-24 px-3 py-2 border rounded-md text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">L</span>
                                </label>
                                <input type="number" step="0.0001" x-model="factorRates.L" @input="calculateUnitPrice"
                                    class="w-24 px-3 py-2 border rounded-md text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">M</span>
                                </label>
                                <input type="number" step="0.0001" x-model="factorRates.M" @input="calculateUnitPrice"
                                    class="w-24 px-3 py-2 border rounded-md text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <label class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">D</span>
                                </label>
                                <input type="number" step="0.0001" x-model="factorRates.D" @input="calculateUnitPrice"
                                    class="w-24 px-3 py-2 border rounded-md text-right focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Middle Column - Calculation -->
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-md font-medium mb-3 text-gray-700 dark:text-gray-300">Calculation</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Factor Rate</label>
                                <input type="number" step="0.0001" x-model="factorRate" @input="calculateUnitPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">SUR</label>
                                <input type="number" step="0.01" x-model="sum_charge" @input="calculateUnitPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                                <input type="number" step="1" min="1" x-model="quantity" @input="calculateUnitPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit Price</label>
                                <input type="number" x-model="unitPrice" readonly
                                    class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md border font-bold text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Override Price</label>
                                <input type="number" step="0.01" x-model="overridePrice" @change="applyOverride"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Notes</label>
                                <textarea x-model="notes" rows="3"
                                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column - Discount -->
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-md font-medium mb-3 text-gray-700 dark:text-gray-300">Discount</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Discount Type</label>
                                <select x-model="discountType" @change="calculateDiscountedPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700">
                                    <option value="percentage">Percentage</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    <span x-text="discountType === 'percentage' ? 'Discount Percentage' : 'Discount Amount'"></span>
                                </label>
                                <div class="relative">
                                    <input type="number" x-model="discountAmount" step="0.01" min="0" @input="calculateDiscountedPrice"
                                        class="w-full pl-3 pr-8 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div x-show="discountType === 'percentage'" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500">%</span>
                                    </div>
                                    <div x-show="discountType === 'fixed'" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-peso-sign text-gray-500"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-md border border-blue-100 dark:border-blue-900">
                                <p class="text-sm text-gray-600 dark:text-gray-300 flex justify-between">
                                    <span>Original Price:</span>
                                    <span class="font-medium">₱<span x-text="formatNumber(getOriginalPrice())"></span></span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 flex justify-between">
                                    <span>Final Price:</span>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">₱<span x-text="formatNumber(getFinalPrice())"></span></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" @click="$dispatch('close')" 
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-md text-sm font-medium transition-colors">
                    Cancel
                </button>
                <button type="button" @click="applyPrice" :disabled="isSubmitting"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors flex items-center justify-center min-w-[100px] disabled:opacity-70">
                    <template x-if="!isSubmitting">
                        <span>Apply Changes</span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center space-x-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Applying...</span>
                        </span>
                    </template>
                </button>
            </div>
        </div>
    </x-modal>

    <x-modal name="add-offer" maxWidth="2xl">
        <div x-data="addCustomOffer()" class="bg-white dark:bg-gray-800 p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Add Offer for: <span x-text="parentItemName" class="text-blue-600 dark:text-blue-300"></span>
            </h2>

            <div class="space-y-4">
                <!-- Item Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Item Name</label>
                    <input type="text" x-model="name"
                        class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter item name">
                </div>

                <!-- Item Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Item Description</label>
                    <textarea x-model="description"
                        class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Enter item description..."></textarea>
                </div>

                <!-- Unit -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Unit</label>
                    <select x-model="unit_id"
                            class="w-full mt-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        <option value="">Select Unit</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-between">
                <button @click="$dispatch('close')" type="button"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Cancel
                </button>

                <button type="button" @click="addCustomItem" :disabled="isSubmitting"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium transition-colors flex items-center justify-center min-w-[100px] disabled:opacity-70">
                    <template x-if="!isSubmitting">
                        <span>Add Offer</span>
                    </template>
                    <template x-if="isSubmitting">
                        <span class="flex items-center space-x-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            <span>Adding...</span>
                        </span>
                    </template>
                </button>

            </div>
        </div>
    </x-modal>


</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('priceTable', () => ({
        items: @json($items),
        parentItems: @json($pricingEstimate->pe_items), // All items for reference
        
        getParentItemName(parentId) {
            const parent = this.parentItems.find(i => i.id === parentId);
            return parent ? parent.item_name : 'Original Item';
        },
        getOffersFor(parentId) {
            return this.items.filter(i => i.is_offer && i.parent_item_id === parentId);
        },
        hasOfferFor(itemId) {
            return this.displayItems().some(i => i.is_offer && i.parent_item_id === itemId);
        },

        getMainItemIndex(currentIdx) {
            // Count how many non-offer items came before this one
            let count = 0;
            for (let i = 0; i <= currentIdx; i++) {
                if (!this.displayItems()[i].is_offer) {
                    count++;
                }
            }
            return count;
        },
        openPriceModal(item) {
            window.dispatchEvent(new CustomEvent('set-price-data', { detail: item }));
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'price-modification' }));
        },
        openOfferModal(parentItem) {
            window.dispatchEvent(new CustomEvent('set-offer-data', { 
                detail: {
                    parent_item_id: parentItem.id,
                    parent_item_name: parentItem.item_name,
                    pricing_estimate_id: parentItem.pricing_estimate_id
                } 
            }));
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'add-offer' }));
        },

        formatCurrency(val) {
            return parseFloat(val || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },
displayItems() {
    const list = [];

    this.items.forEach(parent => {
        if (!parent.is_offer) {
            const offers = this.items.filter(child => child.is_offer && child.parent_item_id === parent.id);

            // Zero out parent pricing if it has offers
            if (offers.length > 0) {
                parent = {
                    ...parent,
                    quantity: 0,
                    unit_price: 0,
                    final_unit_price: 0,
                    discount_amount: 0,
                    price_factor: null,
                    sum_charge: null,
                    factor_rate: null,
                };
            }

            list.push({ ...parent, __type: 'parent' });

            if (offers.length > 0) {
                list.push({ __type: 'offer-header', parent_id: parent.id });

                offers.forEach(offer => {
                    list.push({ ...offer, __type: 'offer' });
                });
            }
        }
    });

    return list;
},
 
        init() {
            // Update existing items
            window.addEventListener('item-updated', e => {
                const updated = e.detail;
                const index = this.items.findIndex(i => i.id === updated.id);
                if (index !== -1) {
                    this.items[index] = updated;
                } else {
                    this.items.push(updated);
                }
            });

            // 🔥 Add this for new offer items
            window.addEventListener('item-added', e => {
                const added = e.detail;

                this.items.push({
                    id: added.id,
                    item_id: added.item_id || null,
                    item_name: added.item_name,
                    item_desc: added.item_description,
                    unit_id: added.unit_id,
                    quantity: added.quantity,
                    unit_price: added.unit_price,
                    final_unit_price: added.final_unit_price || 0,
                    discount_amount: 0,
                    discount_type: 'percentage',
                    is_offer: added.is_offer || false,
                    parent_item_id: added.parent_item_id || null,
                    pe_remarks: added.pe_remarks || '',
                    notes: added.notes || ''
                });
            });
        }
    }));

    Alpine.data('priceModification', () => ({
        currentItem: null,
        factorRates: { R: 0, E: 0, L: 0, M: 0, D: 0 },
        sum_charge: 0,
        factorRate: 1.0,
        quantity: 1,
        unitPrice: 0,
        overridePrice: null,
        notes: '',
        discountType: 'percentage',
        discountAmount: 0,
        isSubmitting: false,

        init() {
            window.addEventListener('set-price-data', (e) => {
                this.currentItem = e.detail;
                
                try {
                    const pf = typeof this.currentItem.price_factor === 'string' 
                        ? JSON.parse(this.currentItem.price_factor)
                        : (this.currentItem.price_factor || {});
                    
                    this.factorRates = {
                        R: pf.R || 0,
                        E: pf.E || 0,
                        L: pf.L || 0,
                        M: pf.M || 0,
                        D: pf.D || 0
                    };
                } catch (e) {
                    console.warn('Failed to parse price_factor:', e);
                }

                this.sum_charge = this.currentItem.sum_charge || 0;
                this.factorRate = this.currentItem.factor_rate || 1.0;
                this.quantity = this.currentItem.quantity || 1;
                this.notes = this.currentItem.notes || '';
                this.unitPrice = this.currentItem.unit_price || 0;
                this.discountType = this.currentItem.discount_type || 'percentage';
                this.discountAmount = this.currentItem.discount_amount || 0;
                
                // this.calculateUnitPrice();
            });
        },

        getOriginalPrice() {
            // If override exists, use that, otherwise use calculated price
            return this.overridePrice !== null && this.overridePrice !== '' 
                ? parseFloat(this.overridePrice)
                : parseFloat(this.unitPrice);
        },

        getFinalPrice() {
            const originalPrice = this.getOriginalPrice();
            if (!this.discountAmount || this.discountAmount <= 0) {
                return originalPrice;
            }
            
            if (this.discountType === 'percentage') {
                return originalPrice * (1 - (parseFloat(this.discountAmount) / 100));
            } else {
                return Math.max(0, originalPrice - parseFloat(this.discountAmount));
            }
        },

        calculateUnitPrice() {
            if (this.overridePrice !== null && this.overridePrice !== '') {
                return; // Don't calculate if override exists
            }
            
            const sumFactors = 
                parseFloat(this.factorRates.R || 0) +
                parseFloat(this.factorRates.E || 0) +
                parseFloat(this.factorRates.L || 0) +
                parseFloat(this.factorRates.M || 0) +
                parseFloat(this.factorRates.D || 0);
            
            const numerator = (sumFactors * parseFloat(this.factorRate || 1)) + parseFloat(this.sum_charge || 0);
            this.unitPrice = parseFloat((numerator / parseFloat(this.quantity || 1)).toFixed(2));
        },

        applyOverride() {
            if (this.overridePrice !== null && this.overridePrice !== '') {
                // When override is set, we don't calculate - just use the override
                this.unitPrice = parseFloat(this.overridePrice).toFixed(2);
            } else {
                // When override is cleared, recalculate
                this.calculateUnitPrice();
            }
        },

        calculateDiscountedPrice() {
            // Just triggers reactivity for the display
        },

        formatNumber(num) {
            return parseFloat(num || 0).toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        async applyPrice() {
            this.isSubmitting = true;
            
            const payload = {
                price_factor: this.factorRates,
                factor_rate: this.factorRate,
                sum_charge: this.sum_charge,
                quantity: this.quantity,
                // unit_price: this.getFinalPrice(),
                unit_price: this.getOriginalPrice(),
                final_unit_price: this.getOriginalPrice(),
                notes: this.notes,
                discount_type: this.discountType,
                discount_amount: this.discountAmount
            };

            try {
                const response = await fetch(`/pricing-estimate-items/${this.currentItem.id}/update-unit-price`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update price');
                }

                Object.assign(this.currentItem, data);
                this.$dispatch('item-updated', data);
                this.$dispatch('close');

                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Price updated successfully!',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });

            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: error.message || 'Failed to update remarks',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });

            } finally {
                this.isSubmitting = false;
            }
        }
    }));

    Alpine.data('addCustomOffer', () => ({ 
        name: '',
        description: '',
        unit_id: '',
        parentItemId: null,
        parentItemName: '',
        pricingEstimateId: null,
        isSubmitting: false,

        init() {
            window.addEventListener('set-offer-data', (event) => {
                console.log('Received offer data:', event.detail); // <-- debug all incoming data
                const { parent_item_id, parent_item_name, pricing_estimate_id } = event.detail;

                this.parentItemId = parent_item_id;
                this.parentItemName = parent_item_name;
                this.pricingEstimateId = pricing_estimate_id;
            });
        },


        async addCustomItem() {
            this.isSubmitting = true;
            if (!this.name || !this.unit_id) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'Missing Fields',
                    text: 'Item name and unit are required.',
                    confirmButtonColor: '#2563eb',
                });
                this.isSubmitting = false;
                return;
            }

            try {
                const response = await fetch(`/pricing-estimate-add-offer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pricing_estimate_id: this.pricingEstimateId,
                        item_name: this.name,
                        item_description: this.description,
                        unit_id: this.unit_id,
                        quantity: 1,
                        unit_price: 0,
                        is_offer: true,
                        parent_item_id: this.parentItemId,
                        pe_remarks: '',
                        notes: ''
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        const messages = Object.values(data.errors).flat().join('\n');
                        throw new Error(messages);
                    }
                    throw new Error(data.message || 'Failed to add offer');
                }

                this.$dispatch('item-added', data);

                await Swal.fire({
                    icon: 'success',
                    title: 'Offer Added',
                    text: 'The custom offer has been saved.',
                    timer: 1500,
                    showConfirmButton: false,
                });

                // Reset and close
                this.name = '';
                this.description = '';
                this.unit_id = '';
                this.$dispatch('close');

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: error.message || 'Something went wrong.',
                });
            } finally{
                this.isSubmitting = false;
            }
        }


    }));

    //for approval
    Alpine.store('PESelection', {
        selected: []
    })
    Alpine.data('PEApproval', () => ({
        showDialog: false,
        action: null,
        revisionReason: '',
        isLoading: false,
        error: null,

        approveUrl: '',
        rejectUrl: '',

        get modalTitle() {
            return this.action === 'approve' ? 'Approve PE?' : 'Disapprove PE?';
        },

        get modalMessage() {
            return this.action === 'approve'
                ? 'Are you sure you want to approve this estimate?'
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
</script>
</x-layouts.app>