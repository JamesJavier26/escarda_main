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
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Price Estimate</h1>
        <p class="text-gray-600 dark:text-gray-400">Review and update pricing for items.</p>
        <div class="text-sm text-gray-300 mt-1">
            {{-- Sales Rep: <span class="font-medium text-white">{{ $pricingEstimate->salesRep->employee->full_name ?? 'N/A' }}</span> --}}
        </div>

    </div>

{{-- Customer & Estimate Info --}}
<div class="grid grid-cols-1 gap-6 w-full mb-6">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Customer & Estimate Details</h2>
            <span class="inline-flex flex-wrap gap-1">
                {{-- Pricing Estimate --}}
                <a href="{{ route('pricing-estimates.show', $pricingEstimate->id) }}" target="_blank"
                class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-blue-800">
                    P.E NO: #{{ $pricingEstimate->pe_no }}
                </a>

                {{-- Quotation(s) --}}
                @foreach($pricingEstimate->quotations ?? [] as $quotation)
                    <a href="{{ route('quotations.edit', $quotation->id) }}" target="_blank"
                    class="bg-green-900 text-green-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-green-800">
                        Quotation NO: #{{ $quotation->quote_no }}
                    </a>

                    {{-- Sales Order --}}
                    @if($quotation->salesOrder)
                        <a href="{{ route('sales-orders.show', $quotation->salesOrder->id) }}" target="_blank"
                        class="bg-yellow-900 text-yellow-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-yellow-800">
                            S.O NO: #{{ $quotation->salesOrder->so_no }}
                        </a>

                        {{-- Job Order --}}
                        @if($quotation->salesOrder->jobOrder)
                            <a href="{{ route('job-orders.show', $quotation->salesOrder->jobOrder->id) }}" target="_blank"
                            class="bg-red-900 text-red-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-red-800">
                                J.O NO: #{{ $quotation->salesOrder->jobOrder->jo_no }}
                            </a>
                        @endif
                    @endif
                @endforeach
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Customer Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">Customer Name</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->customer->name ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Fax No.</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->customer->telefax ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Telephone No.</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->customer->telno ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Email</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->customer->email ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Address</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->customer->address ?? '--' }}</p>
                </div>
            </div>

            {{-- Estimate Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">PR No.</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->pr_no ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Terms</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->terms ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Attention</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->attention ?? '--' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Currency</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->currency->code ?? 'PHP' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400">Tax Status</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->vat ?? $pricingEstimate->customer->tax_status }}</p>
                </div>
            </div>

            {{-- Status / Sales Info --}}
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-400">Sales Representative</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $pricingEstimate->salesRep->employee->full_name ?? 'N/A' }}</p>
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
                    <p class="text-gray-900 dark:text-gray-100" whitespace-pre-line">{{ $pricingEstimate->pe_remarks ?? '--' }}</p>
                </div>
                    <div class="flex flex-col gap-2">
                        <label for="delivery_date" class="text-sm font-medium text-gray-400">Delivery</label>
                        <div class="flex items-center gap-2">
                            <input 
                                type="text" 
                                id="delivery_date"
                                name="delivery_date" 
                                value="{{ $pricingEstimate->delivery_date }}" 
                                class="border rounded p-2"
                            >
                            <button 
                                type="button" 
                                x-data 
                                @click="saveDeliveryDate({{ $pricingEstimate->id }})"
                                class="bg-blue-500 text-white px-3 py-1 rounded"
                            >
                                Save
                            </button>
                        </div>
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
                                        <template x-if="item.pe_remarks">
                                            <div class="text-[10px] text-green-400 dark:text-green-500 italic mt-1"
                                                x-text="item.pe_remarks"></div>
                                        </template>
                                        <template x-if="item.notes">
                                            <div class="text-[10px] text-blue-400 dark:text-blue-500 italic mt-1"
                                                x-text="item.notes"></div>
                                        </template>
                                    </div>
                                </template>

                                <template x-if="item.__type === 'offer-header'">
                                    <div class="uppercase tracking-wide text-xs text-gray-500 dark:text-gray-400">Offer List</div>
                                </template>
                                <template x-if="item.__type === 'offer'">
                                    <div>
                                        <div class="font-semibold" x-text="item.item_name"></div>
                                        <template x-if="item.description">
                                            <div class="text-[10px] text-gray-400 dark:text-gray-500 italic mt-1"
                                                x-text="item.description">
                                            </div>
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
                                        <!-- Final (Discounted) Price -->
                                        <span class="text-base font-semibold text-gray-900 dark:text-white">
                                            {{ $pricingEstimate->currency?->symbol ?? '' }}
                                            <span x-text="formatCurrency(item.final_unit_price)"></span>
                                        </span>

                                        <!-- Show discount if discounted -->
                                        <template x-if="(item.unit_price > item.final_unit_price)">
                                            <div class="flex flex-col text-xs text-green-500 dark:text-green-400">
                                                <!-- Original Price with Strikethrough -->
                                                <span class="line-through text-gray-500 dark:text-gray-400">
                                                    {{ $pricingEstimate->currency?->symbol ?? '' }}<span x-text="formatCurrency(item.unit_price)"></span>
                                                </span>

                                                <!-- Discount Label -->
                                                <span>
                                                    (<span 
                                                        x-text="item.discount_type === 'percentage' 
                                                            ? `${Math.round(((item.unit_price - item.final_unit_price) / item.unit_price) * 100)}% off` 
                                                            : `{{ $pricingEstimate->currency?->symbol ?? '' }}${formatCurrency(item.unit_price - item.final_unit_price)} off`">
                                                    </span>)
                                                </span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </td>
                            
                            <!-- Actions -->
                            <td class="px-4 py-3 text-center space-x-2">
                                <!-- For offer rows -->
                                <template x-if="item.__type === 'offer'">
                                    <div class="flex justify-center space-x-1">
                                        <!-- Add Price always visible -->
                                        <button type="button" 
                                                @click="openPriceModal(item)"
                                                class="text-blue-500 hover:text-blue-700 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-gray-700"
                                                title="Add Price">
                                            <i class="fas fa-calculator"></i>
                                        </button>

                                        @if ($pricingEstimate->pe_status === 'pending' &&
                                            !$pricingEstimate->quotations->contains(fn ($q) => $q->quote_status === 'approved' && $q->is_final))
                                            <button type="button" @click="removeOffer(item)"
                                                class="text-red-500 hover:text-red-700 p-1 rounded hover:bg-red-50 dark:hover:bg-gray-700"
                                                title="Remove Offer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </template>

                                <!-- For parent rows without existing offers -->
                                <template x-if="item.__type === 'parent' && !hasOfferFor(item.id)">
                                    <button type="button" 
                                            @click="openPriceModal(item)"
                                            class="text-blue-500 hover:text-blue-700 transition-colors p-1 rounded hover:bg-blue-50 dark:hover:bg-gray-700"
                                            title="Add Price">
                                        <i class="fas fa-calculator"></i>
                                    </button>
                                </template>

                                <!-- For parent rows -->
                                @if ($pricingEstimate->pe_status === 'pending' &&
                                    !$pricingEstimate->quotations->contains(fn ($q) => $q->quote_status === 'approved' && $q->is_final))
                                    <template x-if="item.__type === 'parent'">
                                        <button type="button" 
                                                @click="openOfferModal(item)"
                                                class="text-green-500 hover:text-green-700 transition-colors p-1 rounded hover:bg-green-50 dark:hover:bg-gray-700"
                                                title="Create Offer">
                                            <i class="fas fa-tag"></i>
                                        </button>
                                    </template>
                                @endif
                            </td>

                        </tr>
                    </template>
                </tbody>
                <tfoot class="bg-gray-100 dark:bg-gray-700 font-semibold">
                    <tr>
                        <td colspan="2" class="px-4 py-2 text-right text-gray-700 dark:text-gray-200">Total</td>
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                            <span x-text="totalQuantity()"></span>
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-900 dark:text-white">
                            {{ $pricingEstimate->currency?->symbol ?? '' }}
                            <span x-text="formatCurrency(totalAmount())"></span>
                        </td>
                        @if (
                            $pricingEstimate->pe_status === 'pending' &&
                            !$pricingEstimate->quotations->contains(fn ($q) => $q->quote_status === 'approved' && $q->is_final)
                        )
                            <td></td>
                        @endif
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- P.E Remarks & Attachments --}}
    <div class="grid grid-cols-1 gap-6 w-full mb-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-600 pb-2">P.E. Remarks & Attachments</h2>

            {{-- Remarks --}}
            <div class="mb-6">
                <p class="text-sm font-medium text-gray-400">Remarks</p>
                <p class="text-gray-900 dark:text-gray-100" whitespace-pre-line">
                    {{ $pricingEstimate->pe_remarks ?? '—' }}
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

        <a href="{{ route('reports.pricing-preview', $pricingEstimate->pe_no) }}" target="_blank"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg 
                focus:outline-none focus:ring-2 focus:ring-blue-500 transition flex items-center gap-2"
            title="Print Pricing Preview">
            <i class="fas fa-file-invoice-dollar text-sm"></i>
            Pricing Preview
        </a>

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
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Client Notes</label>
                                <textarea x-model="notes" rows="3"
                                    class="w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100 text-sm"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Internal Notes</label>
                                <textarea x-model="internal_notes" rows="3"
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
                                    <span class="font-medium">{{ $pricingEstimate->currency?->symbol ?? '' }}<span x-text="formatNumber(getOriginalPrice())"></span></span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 flex justify-between">
                                    <span>Final Price:</span>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">{{ $pricingEstimate->currency?->symbol ?? '' }}<span x-text="formatNumber(getFinalPrice())"></span></span>
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

                @if($pricingEstimate->pe_status !== 'approved')
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
                @endif
            </div>
        </div>
    </x-modal>

   <x-modal name="add-offer" maxWidth="6xl">
    <div x-data="itemSearch()" class="bg-white dark:bg-gray-800 p-6 max-h-[80vh] overflow-y-auto">

        <!-- Custom Item Section -->
        <div class="border-b border-gray-300 dark:border-gray-600 pt-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Add Custom Item</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Item Name -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item Name *</label>
                    <input type="text" x-model="customName"
                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2"
                           placeholder="Enter item name">
                </div>

                <!-- Item Unit -->
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item Unit *</label>
                    <select x-model="customUnit"
                            class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
                        <option value="" disabled selected>-- Select unit --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->short_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea x-model="customDescription" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2"
                              placeholder="Optional description"></textarea>
                </div>
            </div>

            <div class="mt-6 mb-3 flex justify-end">
                <button @click="addCustomItem" :disabled="!customName || !customUnit"
                        class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
                    Add Custom Item
                </button>
            </div>
        </div>

        <!-- Existing Items Section -->
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Items</h2>

        <!-- Search Controls -->
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-1/3">
                <select x-model="searchField"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
                    <option value="all">All Fields</option>
                    <option value="number">Item Number</option>
                    <option value="name">Name</option>
                    <option value="description">Description</option>
                </select>
            </div>

            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                              clip-rule="evenodd"/>
                    </svg>
                </div>
                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchItems"
                       placeholder="Search items..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <!-- Items List -->
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
            <template x-if="isLoading">
                <div class="flex justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </template>

            <template x-if="!isLoading && items.length > 0">
                <ul class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                    <template x-for="item in items" :key="item.id">
                        <li class="py-4 px-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate"
                                       x-text="item.number"></p>
                                    <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold" x-text="item.name"></p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate"
                                       x-text="item.description"></p>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <button @click="addItem(item)"
                                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Add
                                    </button>
                                </div>
                            </div>
                        </li>
                    </template>
                </ul>
            </template>

            <template x-if="!isLoading && items.length === 0 && searchQuery.length > 0">
                <div class="text-center py-4 text-gray-500 dark:text-gray-400">No items found</div>
            </template>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
        
            <!-- Desktop -->
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Showing <span class="font-medium" x-text="(currentPage - 1) * perPage + 1"></span> to
                    <span class="font-medium" x-text="Math.min(currentPage * perPage, totalItems)"></span> of
                    <span class="font-medium" x-text="totalItems"></span> results
                </p>

                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Prev -->
                    <button @click="prevPage()" :disabled="currentPage === 1"
                            class="px-2 py-2 rounded-l-md border bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50">
                        <span class="sr-only">Previous</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>

                    <!-- Page Numbers -->
                    <template x-for="page in getPageNumbers()" :key="page">
                        <button @click="goToPage(page)"
                                :class="currentPage === page ? 'bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-600 text-blue-600 dark:text-blue-300' : 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600'"
                                class="px-4 py-2 border text-sm font-medium"
                                x-text="page"></button>
                    </template>

                    <!-- Next -->
                    <button @click="nextPage()" :disabled="currentPage === lastPage"
                            class="px-2 py-2 rounded-r-md border bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 disabled:opacity-50">
                        <span class="sr-only">Next</span>
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </button>
                </nav>
                    <div class="mt-6 flex justify-end space-x-3">
                    <button 
                        @click="$dispatch('close')"
                        type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-modal>



</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('priceTable', () => ({
            items: @json($items).map(i => ({
        ...i,
        description: i.description ?? i.item_description ?? i.item_desc ?? ''
        })),
        parentItems: @json($pricingEstimate->pe_items),
        removedOffers: [],

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

        removeOffer(item) {
        if (!confirm('Are you sure you want to delete this offer?')) return;

        // Remove locally
        this.items = this.items.filter(i => i.id !== item.id);

        // Optional: track removed offers if you need to batch delete later
        if (!this.removedOffers) this.removedOffers = [];
        if (item.id) this.removedOffers.push(item.id);

        // Delete from backend immediately via fetch
        if (item.id) {
            fetch(`/pricing-estimates/item/${item.id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    alert('Failed to remove offer on server.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('An error occurred while removing the offer.');
            });
        }
    },

        getMainItemIndex(currentIdx) {
            let count = 0;
            for (let i = 0; i <= currentIdx; i++) {
                if (!this.displayItems()[i].is_offer) count++;
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
                
        totalAmount() {
            return this.displayItems()
                .filter(i => i.__type === 'parent' || i.__type === 'offer')
                .reduce((sum, i) => {
                    const price = Number(i.final_unit_price ?? i.unit_price ?? 0) || 0;
                    const qty   = Number(i.quantity) || 0;
                    return sum + price * qty;
                }, 0);
        },

        totalQuantity() {
            return this.displayItems()
                .filter(i => i.__type === 'parent' || i.__type === 'offer')
                .reduce((sum, i) => sum + (Number(i.quantity) || 0), 0);
        },

        displayItems() {
            const list = [];

            this.items.forEach(parent => {
                if (!parent.is_offer) {
                    const offers = this.items.filter(child => child.is_offer && child.parent_item_id === parent.id);

                    if (offers.length > 0) {
                        parent = { ...parent, quantity: 0, unit_price: 0, final_unit_price: 0, discount_amount: 0 };
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
                if (index !== -1) this.items[index] = updated;
                else this.items.push(updated);
            });

            // Add new offer items
            window.addEventListener('item-added', e => {
            const added = e.detail;

            // Make sure description is always set
            const description = added.description ?? added.item_description ?? '';

                this.items.push({
                    id: added.id,
                    item_id: added.item_id || null,
                    item_name: added.item_name,
                    item_desc: description,            // For consistency with existing code
                    item_description: description,     // Populate all keys used in template
                    description: description,
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
        internal_notes: '',
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
                this.internal_notes = this.currentItem.internal || '';
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
        this.overridePrice = '';

        // Calculate final price with discount
        const finalPrice = this.getFinalPrice();
        const originalPrice = this.getOriginalPrice();

        if (!this.currentItem.id) {
            // Unsaved item: just update locally
            this.currentItem.unit_price = originalPrice;      // keep original for cross-out
            this.currentItem.final_unit_price = finalPrice;   // show discounted price
            this.$dispatch('item-updated', { ...this.currentItem });
            this.$dispatch('close');

            // ✅ Show success toast even for unsaved items
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Price updated successfully!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
            });

            this.isSubmitting = false;
            return;
        }

        // Payload for saved items
        const payload = {
            price_factor: this.factorRates,
            factor_rate: this.factorRate,
            sum_charge: this.sum_charge,
            quantity: this.quantity,
            unit_price: originalPrice,      // for cross-out
            final_unit_price: finalPrice,   // discounted
            notes: this.notes,
            internal_notes: this.internal_notes,
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
            if (!response.ok) throw new Error(data.message || 'Failed to update price');

            Object.assign(this.currentItem, data);
            this.$dispatch('item-updated', data);
            this.$dispatch('close');

            // ✅ Success toast for saved items
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Price updated successfully!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
            });
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                toast: true,
                icon: 'error',
                title: error.message || 'Failed to update price',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
            });
        } finally {
            this.isSubmitting = false;
        }
    }


    }));

    Alpine.data('itemSearch', () => ({
        searchQuery: '',
        items: [],
        isLoading: false,
        currentPage: 1,
        lastPage: 1,
        totalItems: 0,
        perPage: 10,
        searchField: 'all',
        customName: '',
        customDescription: '',
        customUnit: '',
        parentItemId: null,
        parentItemName: '',
        pricingEstimateId: null,
        isSubmitting: false,

        init() {
            // Listen for parent item info from event
            window.addEventListener('set-offer-data', (event) => {
                const { parent_item_id, parent_item_name, pricing_estimate_id } = event.detail;
                this.parentItemId = parent_item_id;
                this.parentItemName = parent_item_name;
                this.pricingEstimateId = pricing_estimate_id;

                // Fetch first page when modal opens
                this.searchItems();
            });
        },

        async searchItems(resetPage = true) {
            if (resetPage) this.currentPage = 1;
            this.isLoading = true;

            try {
                const response = await fetch(`/items/search?q=${encodeURIComponent(this.searchQuery)}&page=${this.currentPage}&field=${this.searchField}`);
                const data = await response.json();

                this.items = data.data || [];
                this.currentPage = data.current_page || 1;
                this.lastPage = data.last_page || 1;
                this.totalItems = data.total || 0;
                this.perPage = data.per_page || 10;

                console.log('Items loaded:', { currentPage: this.currentPage, lastPage: this.lastPage, totalItems: this.totalItems });
            } catch (error) {
                console.error('Error fetching items:', error);
                this.items = [];
                this.currentPage = this.lastPage = 1;
                this.totalItems = 0;
            } finally {
                this.isLoading = false;
            }
        },

        nextPage() {
            if (this.currentPage < this.lastPage) {
                this.currentPage++;
                this.searchItems(false);
            }
        },

        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.searchItems(false);
            }
        },

        goToPage(page) {
            if (page >= 1 && page <= this.lastPage) {
                this.currentPage = page;
                this.searchItems(false);
            }
        },

        getPageNumbers() {
            const pages = [];
            if (this.lastPage <= 5) {
                for (let i = 1; i <= this.lastPage; i++) pages.push(i);
            } else {
                let start = Math.max(this.currentPage - 2, 1);
                let end = Math.min(this.currentPage + 2, this.lastPage);
                if (this.currentPage <= 3) end = 5;
                if (this.currentPage >= this.lastPage - 2) start = this.lastPage - 4;
                for (let i = start; i <= end; i++) pages.push(i);
            }
            return pages;
        },

        async addItem(item) {
            if (!this.parentItemId || !this.pricingEstimateId) {
                console.error('Parent or pricing estimate missing');
                return;
            }

            try {
                const res = await fetch('/pricing-estimate-add-offer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pricing_estimate_id: this.pricingEstimateId,
                        item_id: item.id,
                        item_name: item.name || item.item_name,
                        item_description: item.description || item.item_description || '',
                        unit_id: item.unit_id,
                        quantity: 1,
                        unit_price: item.price || 0,
                        final_unit_price: item.price || 0, // backend can override
                        discount_type: 'percentage',
                        discount_amount: 0,
                        is_offer: true,
                        parent_item_id: this.parentItemId,
                        pe_remarks: '',
                        notes: '',
                        internal_notes: ''
                    })
                });

                const data = await res.json();
                if (!res.ok) {
                    console.error('Validation failed:', data);
                    throw new Error(data.message || 'Failed to add offer');
                }

                // Ensure frontend can display it
                this.items.push({
                    ...data,
                    __type: 'offer'
                });

                // Dispatch Alpine event
                this.$dispatch('item-added', data);

                // Clear search
                this.searchQuery = '';

                // Success toast
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: `Item "${item.name}" added successfully!`,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });

            } catch (e) {
                console.error(e);
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: e.message || 'Something went wrong',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });
            }
        },


        async addCustomItem() {
            if (!this.customName || !this.customUnit) {
                Swal.fire({ icon: 'warning', title: 'Missing Fields', text: 'Item name and unit are required.' });
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch('/pricing-estimate-add-offer', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        pricing_estimate_id: this.pricingEstimateId,
                        item_name: this.customName,
                        item_description: this.customDescription,
                        unit_id: this.customUnit,
                        quantity: 1,
                        unit_price: 0,
                        final_unit_price: 0,
                        is_offer: true,
                        parent_item_id: this.parentItemId,
                        pe_remarks: '',
                        notes: '',
                        internal_notes: ''
                    })
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to add custom offer');

                this.$dispatch('item-added', data);

                Swal.fire({
                toast: true,
                icon: 'success',
                title: `Custom item "${data.item_name}" added successfully!`,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                },
            });

                this.customName = '';
                this.customDescription = '';
                this.customUnit = '';
            } catch (e) {
                console.error(e);
                Swal.fire({ icon: 'error', title: 'Error', text: e.message || 'Something went wrong' });
            } finally {
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
function saveDeliveryDate(id) {
    const input = document.querySelector('[name="delivery_date"]');
    if (!input || !input.value.trim()) {
        Swal.fire({
            toast: true,
            icon: 'warning',
            title: 'Please enter a delivery date.',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
        });
        return;
    }

    const formData = new FormData();
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    formData.append('delivery_date', input.value.trim());

    fetch(`/pricing-estimates/${id}/save-delivery-date`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(res => res.json())
    .then(data => { 
        if (data.success) {
            Swal.fire({
                toast: true,
                icon: 'success',
                title: 'Delivery Date updated successfully!',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
            });
        } else {
            Swal.fire({
                toast: true,
                icon: 'error',
                title: data.message || 'Failed to save.',
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
            });
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Something went wrong.',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: { popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white' }
        });
    });
}

</script>
</x-layouts.app>