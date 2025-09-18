@php
    $editing = $sales_order->exists;
    $initialCustomer = $editing && $sales_order->customer
        ? $sales_order->customer->toJson()
        : 'null';

    $initialRows = [];

    if (old('new_items')) {
        foreach (old('new_items') as $uid => $item) {
            $initialRows[] = [
                'local_uid' => is_numeric($uid) ? (int)$uid : (int)substr($uid, 4), // strip prefix if needed
                'item_id' => $item['item_id'] ?? null,
                'name' => $item['name'] ?? '',
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['final_unit_price'] ?? $item['unit_price'] ?? 0,
                'unit_id' => $item['unit_id'] ?? null,
                'unit_name' => $item['unit_name'] ?? '',
            ];
        }
    } elseif ($editing) {
        $initialRows = $sales_order->jobOrder->jo_items->map(function ($item, $index) {
            $pricingItem = $item->pricingEstimateItem;

            return [
                'pricing_item_id' => $pricingItem?->id,
                'item_id' => $item->item_id,
                'name' => $item->item_name,
                'description' => $item->item_description,
                'quantity' => $item->quantity,
                'unit_price' => $pricingItem?->final_unit_price ?? $item->unit_price,
                'final_unit_price' => $pricingItem?->final_unit_price ?? $item->unit_price,
                'unit_id' => $item->unit_id,
                'unit_name' => $item->unit->short_name ?? '',
                'local_uid' => $index, // numeric index safe for nextLocalId
            ];
        })->toArray();
    }
@endphp


<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
    }

    /* Firefox */
    input[type=number] {
    -moz-appearance: textfield;
    }
</style>
<div x-data="">
    <form
        {{-- {{ $attributes->merge(['class' => 'space-y-6']) }} --}}
         x-data="saveDraftHandler" @submit.prevent="confirmSave" 
        class="space-y-6"
        method="POST"
        enctype="multipart/form-data"
        action="{{ $editing ? route('sales-orders.update', $sales_order) : route('sales-orders.store') }}"
    >
        @csrf
        @if($editing) @method('PUT') @endif


        <div x-data="customerLookup({{ $initialCustomer }}, {{ $editing ? 'true' : 'false' }})">
            {{-- Search box only in create mode --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative" x-show="!locked">
                <div class="col-span-1 space-y-4">
                    <div class="relative mb-6">
                        <label for="customer_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Search Customer <span class="text-red-500">*</span>
                        </label>

                        {{-- Hidden SO No if needed --}}
                        <input type="hidden" name="so_no" value="{{ $so_no ?? '' }}"/>

                        {{-- Visible input --}}
                        <input id="customer_search"
                            x-model="query"
                            @input.debounce.400ms="search"
                            type="text"
                            placeholder="Type name, address, email"
                            class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                    focus:ring-blue-500 focus:border-transparent">

                        {{-- hidden FK that will actually be saved --}}
                          <input type="hidden" name="customer_id" :value="chosen?.id">
                          {{-- <input type="text" name="customer_id" :value="1"> --}}

                        {{-- Dropdown --}}
                        <template x-if="showResults">
                            <ul class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 max-h-60 overflow-y-auto">
                                <template x-for="cust in results" :key="cust.id">
                                    <li @click="pickCustomer(cust)" class="px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 cursor-pointer">
                                        <p class="font-medium" x-text="cust.name"></p>
                                        <p class="text-xs text-gray-500" x-text="cust.address"></p>
                                        <p class="text-xs text-gray-400" x-text="cust.telno"></p>
                                    </li>
                                </template>
                                <li x-show="results.length === 0" class="px-4 py-2 text-sm text-gray-500">No matches</li>
                            </ul>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Customer Info Card (Both Modes) --}}
            <div class="grid grid-cols-1 gap-6 w-full" x-init="init()" x-on:item-added.window="receiveItems($event.detail)"id="itemsTable">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                    <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                        <h2 class="text-xl font-semiboldtext-gray-900 dark:text-gray-100">Customer Details</h2>
                        <span class="inline-flex flex-wrap gap-1">
                            {{-- Pricing Estimate --}}
                            @if($sales_order->quotation?->pricing_estimate)
                                <a href="{{ url('pricing-estimates/' . $sales_order->quotation->pricing_estimate->id . '/price-entry') }}" target="_blank"
                                class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-blue-800">
                                    P.E NO: #{{ $sales_order->quotation->pricing_estimate->pe_no }}
                                </a>
                            @endif

                            {{-- Quotation --}}
                            @if($sales_order->quotation)
                                <a href="{{ route('quotations.edit', $sales_order->quotation->id) }}" target="_blank"
                                class="bg-green-900 text-green-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-green-800">
                                    Quotation NO: #{{ $sales_order->quotation->quote_no }}
                                </a>
                            @endif

                            {{-- Sales Order (only if approved) --}}
                            @if($sales_order && $sales_order->so_status === 'approved')
                                <a href="{{ route('sales-orders.show', $sales_order->id) }}" target="_blank"
                                class="bg-yellow-900 text-yellow-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-yellow-800">
                                    S.O NO: #{{ $sales_order->so_no }}
                                </a>

                                {{-- Job Order (only if SO is approved) --}}
                                @if($sales_order->jobOrder)
                                    <a href="{{ route('job-orders.show', $sales_order->jobOrder->id) }}" target="_blank"
                                    class="bg-red-900 text-red-100 text-xs font-medium px-2.5 py-1 rounded hover:bg-red-800">
                                        J.O NO: #{{ $sales_order->jobOrder->jo_no }}
                                    </a>
                                @endif
                            @endif
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Name</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="chosen?.name ?? '--'">--</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-400">Telephone</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="chosen?.telno ?? '--'">--</p>
                            </div>
                            <div x-data="{ editing: {{ $editing ? 'true' : 'false' }} }">
                                <label for="tax_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tax Status</label>
                                <select name="tax_status"
                                    x-model="$store.order.tax_status"
                                    :disabled="editing"
                                    class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="editing ? 'cursor-not-allowed opacity-50 pointer-events-none' : ''">
                                    <option value="">-</option>
                                    <option value="Zero Rated">Zero Rated</option>
                                    <option value="VAT Inclusive">VAT Inclusive</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Email</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="chosen?.email ?? '--'">--</p>
                            </div>

                            <div>
                                <p class="text-sm font-medium text-gray-400">Address</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="chosen?.address ?? '--'">--</p>
                            </div>

                            <div x-data="{ editing: {{ $editing ? 'true' : 'false' }} }">
                            <label for="terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Terms</label>
                            <select name="terms"
                                    :value="chosen?.terms_of_payment || ''"
                                    @change="if (chosen) chosen.terms_of_payment = $event.target.value"
                                    :disabled="editing"
                                    class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="editing ? 'cursor-not-allowed opacity-50 pointer-events-none' : ''">
                                <option value="">-</option>
                                <option value="COD">COD</option>
                                <option value="30 days">30 days</option>
                                <option value="60 days">60 days</option>
                                <option value="90 days">90 days</option>
                            </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Other Info --}} 
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Other Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Left Column --}}
                <div class="space-y-4">
                    <x-forms.input 
                        label="P.R No:" 
                        name="pr_no" 
                        type="text" 
                        :value="old('pr_no', $sales_order->pr_no)" 
                    />

                    <x-forms.input 
                        label="Attention:" 
                        name="attention" 
                        type="text" 
                        :value="old('attention', $sales_order->attention)" 
                    />

                    {{-- Move P.O# to bottom (solo) --}}
                    <div>
                        <label for="po_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            P.O# <span class="text-red-500">*</span>
                        </label>
                        <input id="po_no" name="po_no" type="text"
                            value="{{ old('po_no', $sales_order->po_no) }}"
                            placeholder="Enter P.O"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('po_no')
                            ])>
                        @error('po_no') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="space-y-4"> 
                    <x-forms.input label="Delivery Date" name="delivery_date" type="date" :value="old('delivery_date', $sales_order->delivery_date)" />   
                    <div>
                        <label for="currency_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                        <select name="currency_id" 
                            class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @foreach ($currencies as $currency)
                                <option 
                                    value="{{ $currency->id }}" 
                                    {{ old('currency_id', $sales_order->currency_id) == $currency->id ? 'selected' : '' }}>
                                    {{ $currency->symbol }} — {{ $currency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-500"></i>
                        {{ $title ?? 'Items' }}
                    </h3>
                    <div class="relative flex items-center gap-3">
                        {{-- <div class="relative">
                            <input type="text"
                                placeholder="{{ $placeholder ?? 'Search items...' }}"
                                wire:model.live="search"
                                class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div> --}}
                        @if($sales_order->so_status == 'draft')
                        <button type="button"
                                @click="$dispatch('open-modal', 'item-picker')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                    text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500
                                    transition-colors duration-200">
                            <i class="fas fa-plus"></i>
                            Add Items
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="p-4 overflow-x-auto">
                <div x-data="itemsTable({ initialRows: @js($initialRows) })" x-init="init()" x-on:item-added.window="receiveItems($event.detail)"id="itemsTable">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <x-table-column field="item_number" class="w-16">#</x-table-column>
                                <x-table-column field="item_name">Item Name</x-table-column>
                                <x-table-column field="quantity" class="text-center">Qty</x-table-column>
                                <x-table-column field="unit_price" class="text-right">Unit Price</x-table-column>
                                <x-table-column field="total" class="text-right">Total</x-table-column>
                                {{-- @if($sales_order->so_status == 'draft') --}}
                                    <x-table-column class="w-20 text-right">Actions</x-table-column>
                                {{-- @endif --}}
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, idx) in rows" :key="row.pricing_item_id ?? row.local_uid">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" x-show="!row._delete">
                                    <td x-text="idx + 1" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"></td>
                                    
                                    <td class="px-4 py-3">
                                        <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                            <input type="text" x-model="row.name"
                                                class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                                placeholder="Unnamed Item" />
                                            <textarea x-model="row.description"
                                                class="mt-1 text-xs w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                                rows="2" placeholder="Item description"></textarea>
                                        </div>
    
                                    </td>

                                    <!-- Quantity -->
                                    <td class="px-4 py-3 text-center">
                                        
                                        {{-- <input type="number" x-model.number="row.quantity"
                                            class="w-full px-2 py-1 text-sm border rounded text-center dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                            min="1" step="1" /> --}}
                                        <div class="flex items-center justify-center h-8">
                                            <!-- Minus Button -->
                                            <button type="button" 
                                                    @click="row.quantity = Math.max(1, (parseInt(row.quantity) || 1) - 1)"
                                                    class="h-full px-2 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-r-0 rounded-l-md dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                            <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            
                                            <!-- Quantity Input -->
                                            <input type="number" 
                                                x-model="row.quantity" 
                                                min="1"
                                                max="999"
                                                class="h-full w-12 px-1 text-center text-sm border-t border-b border-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500"
                                                onkeydown="return event.keyCode !== 38 && event.keyCode !== 40" 
                                                style="-moz-appearance: textfield;">
                                            
                                            <!-- Plus Button -->
                                            <button type="button" 
                                                    @click="row.quantity = (parseInt(row.quantity) || 1) + 1"
                                                    class="h-full px-2 flex items-center justify-center text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 border border-l-0 rounded-r-md dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                                            <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="row.unit_name || row.unit || ''"></div>
                                    </td>

                                    <!-- Unit Price -->
                                    <td class="px-4 py-3 text-right">
                                        @if(!$editing)
                                            <input type="number"
                                                x-model.number="row.unit_price"
                                                min="0"
                                                step="0.01"
                                                class="w-full px-2 py-1 text-sm text-right border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
                                        @else
                                            <span x-text="formatCurrency(row.unit_price)"></span>
                                        @endif
                                    </td>


                                    <!-- Total -->
                                    <td class="px-4 py-3 text-right" x-text="formatCurrency(row.quantity * row.unit_price)"></td>

                                    {{-- @if($sales_order->so_status == 'draft') --}}
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" @click="deleteItem(row, idx)"
                                            class="text-red-500 hover:text-red-700 transition-colors">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                    {{-- @endif --}}

                                    <!-- Hidden Inputs -->
                                    <template x-for="[name, value] in hiddenFields(row)" :key="name">
                                        <input type="hidden" :name="name" :value="value">
                                    </template>
                                </tr>

                            </template>

                            <tr x-show="rows.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center gap-2 mt-2 mb-2">
                                        <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                        <span>No items added yet</span>
                                        <button type="button" @click="$dispatch('open-modal', 'item-picker')"
                                            class="text-blue-500 hover:text-blue-700 text-sm font-medium mt-2">
                                            <i class="fas fa-plus mr-1"></i> Add your first item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>

                        <!-- Subtotal, VAT, Total -->
                        <tfoot class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-semibold text-sm text-gray-700 dark:text-gray-300">Total Vatable Sales:</td>
                                <td class="px-4 py-2 text-right text-sm text-gray-800 dark:text-white" x-text="formatCurrency(subtotal())"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-semibold text-sm text-gray-700 dark:text-gray-300">VAT:</td>
                                <td class="px-4 py-2 text-right text-sm text-gray-800 dark:text-white" x-text="formatCurrency(vat())"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-4 py-2 text-right font-bold text-sm text-gray-700 dark:text-gray-300">Total Sales Amount:</td>
                                <td class="px-4 py-2 text-right font-bold text-sm text-gray-900 dark:text-white" x-text="formatCurrency(total())"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>
        <!-- Remarks & Attachments -->                                  
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Remarks --}}
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Remarks</h2>
                <textarea name="remarks" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm 
                        focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                >{{ old('remarks', $sales_order->remarks) }}</textarea>
            </div>

            {{-- Attachments --}}
            <div 
                x-data="attachmentsHandler()"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4"
            >
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Attachments</h2>

                <input type="file" name="so_attachment[]" multiple x-ref="fileInput" class="hidden" @change="pickFiles">

                <button type="button" @click="$refs.fileInput.click()"
                    class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-paperclip mr-1"></i> Select files
                </button>

                {{-- Existing attachments (edit mode) --}}
                @if($editing && $sales_order->attachments->count())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-4">
                        @foreach($sales_order->attachments as $file)
                            <div x-data="{ keep: true }" x-show="keep"
                                class="relative border border-gray-300 dark:border-gray-600 p-3 rounded-md">
                                
                                <input type="hidden" name="keep_attachments[]" :value="keep ? '{{ $file->id }}' : 0">

                                <button type="button" @click="keep = false"
                                    class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700"
                                    title="Remove this file">
                                    <i class="fas fa-times text-xs"></i>
                                </button>

                                @if(\Illuminate\Support\Str::startsWith($file->metadata['mime_type'] ?? '', 'image/'))
                                    <img src="{{ Storage::url($file->metadata['path']) }}" class="w-full h-40 object-contain rounded">
                                @elseif(($file->metadata['mime_type'] ?? '') === 'application/pdf')
                                    <iframe src="{{ Storage::url($file->metadata['path']) }}" class="w-full h-40 rounded"></iframe>
                                @else
                                    <div class="flex items-center justify-center h-40 bg-gray-100 dark:bg-gray-700 rounded">
                                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                                    </div>
                                @endif

                                <p class="mt-2 text-xs truncate">{{ $file->metadata['original_name'] ?? basename($file->metadata['path']) }}</p>

                                <div class="mt-3">
                                    <label for="file-label-{{ $file->id }}" class="block text-xs font-medium text-gray-600 dark:text-gray-400">
                                        Label
                                    </label>
                                    <input type="text"
                                        id="file-label-{{ $file->id }}"
                                        name="existing_file_labels[{{ $file->id }}]"
                                        value="{{ $file->label }}"
                                        placeholder="Enter label/description"
                                        class="mt-1 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 
                                            rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500" 
                                        aria-label="Label for attachment {{ $file->id }}">
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- New uploads --}}
                <template x-if="files.length">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-4">
                        <template x-for="(entry, idx) in files" :key="idx">
                            <div class="relative border border-gray-300 dark:border-gray-600 p-3 rounded-md">

                                <button type="button" @click="removeFile(idx)"
                                    class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-700">
                                    <i class="fas fa-times text-xs"></i>
                                </button>

                                <template x-if="entry.file.type.startsWith('image/')">
                                    <img :src="URL.createObjectURL(entry.file)" class="w-full h-40 object-contain rounded">
                                </template>
                                <template x-if="entry.file.type==='application/pdf'">
                                    <iframe :src="URL.createObjectURL(entry.file)" class="w-full h-40 rounded"></iframe>
                                </template>
                                <template x-if="!entry.file.type.startsWith('image/') && entry.file.type!=='application/pdf'">
                                    <div class="flex items-center justify-center h-40 bg-gray-100 dark:bg-gray-700 rounded">
                                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                                    </div>
                                </template>

                                <p class="mt-2 text-xs truncate" x-text="entry.file.name"></p>

                                <div class="mt-3">
                                    <label :for="'file-label-new-' + idx" class="block text-xs font-medium text-gray-600 dark:text-gray-400">
                                        Label
                                    </label>
                                    <input type="text"
                                        :id="'file-label-new-' + idx"
                                        class="mt-1 w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 
                                            rounded-lg dark:bg-gray-700 dark:text-gray-100 focus:ring focus:ring-blue-500 focus:border-blue-500" 
                                        x-model="entry.label"
                                        placeholder="Enter label/description">
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

        </div>

 
        @if($editing)
        @hasrole('super-admin|admin')
        <div class="grid grid-cols-1 mt-8">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4"
                x-data="commentSection({{ Js::from($comments) }}, {{ $sales_order->id }})">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Comments</h2>

                <!-- Comment List -->
                <div x-ref="commentList" class="space-y-3 max-h-64 overflow-y-auto pr-2">
                    <template x-for="(comment, index) in comments" :key="comment.id">
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <p class="font-medium text-gray-800 dark:text-gray-200"
                                x-text="comment.user.username ? comment.user.username : (comment.user.name ?? 'Unknown')"></p>
                                <span class="text-xs text-gray-500 dark:text-gray-400"
                                    x-text="formatDate(comment.created_at)"></span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300" x-text="comment.comment"></p>
                        </div>
                    </template>

                    <div x-show="comments.length === 0"
                        class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No comments yet
                    </div>
                </div>

                <!-- New Comment Form -->
                <div class="mt-4">
                    <label for="new-comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Add Comment
                    </label>
                    <div class="flex space-x-2">
                        <input
                            x-model="newComment"
                            type="text"
                            id="new-comment"
                            placeholder="Type your comment..."
                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                        >
                        <button
                            @click="addComment"
                            :disabled="!newComment.trim() || isLoading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            <span x-show="!isLoading">Add</span>
                            <span x-show="isLoading">Adding...</span>
                        </button>
                    </div>
                    <p x-show="error" class="mt-1 text-sm text-red-500" x-text="error"></p>
                </div>
            </div>
        </div>
        @endhasrole
        @endif

        <div class="flex flex-col md:flex-row justify-between items-center mt-5 gap-4">
            {{-- Left side: Back, Print, Save Draft --}}
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('sales-orders.index') }}"
                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                        text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                        border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                        focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Back to List
                </a>

                @if($editing)
                    <a href="{{ route('reports.salesOrder', $sales_order->so_no) }}" target="_blank"
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                            text-gray-700 dark:text-gray-200 bg-white dark:bg-blue-700
                            border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-blue-600
                            focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Print
                    </a>
                @endif

                @if($sales_order->so_status !== 'approved')
                    <button type="submit"
                            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        Save Draft
                    </button>
                @endif
            </div>

            {{-- Right side: Submit for Approval --}}
            @if($editing && $sales_order->so_status !== 'pending')
                <div x-data="soApproval({ soID: {{ $sales_order->id }} })">
                    <button type="button"
                            @click="submitSOApproval"
                            :disabled="isSubmitting"
                            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span x-show="!isSubmitting">Submit For Approval</span>
                        <span x-show="isSubmitting">Submitting...</span>
                    </button>
                </div>
            @endif
        </div>


   
    </form>
      @if($editing)
    <form id="activate-job-order-form"
        action="{{ route('job-orders.activate', $sales_order->jobOrder) }}"
        method="POST" style="display: none;">
        @csrf
    </form>
    @endif

    {{-- @if($sales_order->jobOrder->approval_status === 'draft')
        <form action="{{ route('job-orders.activate', $sales_order->jobOrder) }}" method="POST" class="inline">
            @csrf
            <button type="submit"
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    title="Create Job Order"
                    onclick="return confirm('Create Job Order #{{ $sales_order->jobOrder->jo_no }}?')">
                <i class="fas fa-tasks"></i> Create Job Order
            </button>
        </form>
    @endif --}}

    <x-modal name="item-picker" maxWidth="6xl">
            <div x-data="itemSearch()" class="bg-white dark:bg-gray-800 p-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Items</h2>
                
                <!-- Search input -->
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-1/3">
                        <select x-model="searchField" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="all">All Fields</option>
                            <option value="number">Item Number</option>
                            <option value="name">Name</option>
                            <option value="description">Description</option>
                        </select>
                    </div>
                    
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            x-model="searchQuery" 
                            @input.debounce.300ms="searchItems" 
                            placeholder="Search..." 
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                    </div>
                </div>



                <!-- Search results -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <template x-if="isLoading">
                        <div class="flex justify-center py-8">
                            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </template>
                    
                    <template x-if="!isLoading && items.length > 0">
                        <div>
                            <ul class="divide-y divide-gray-200 dark:divide-gray-700 max-h-96 overflow-y-auto">
                                <template x-for="item in items" :key="item.id">
                                    <li class="py-4 px-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" x-text="item.number"></p>
                                                <p class="text-sm text-gray-900 dark:text-gray-100 font-semibold" x-text="item.name"></p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate" x-text="item.description"></p>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <button 
                                                    @click="addItem(item)"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                                >
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            
                            <!-- Pagination -->
                            <div class="flex items-center justify-between mt-4 px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex-1 flex justify-between sm:hidden">
                                    <button 
                                        @click="prevPage()" 
                                        :disabled="currentPage === 1"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    >
                                        Previous
                                    </button>
                                    <button 
                                        @click="nextPage()" 
                                        :disabled="currentPage === lastPage"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600"
                                    >
                                        Next
                                    </button>
                                </div>
                                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                    <div>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">
                                            Showing <span class="font-medium" x-text="(currentPage - 1) * 10 + 1"></span> to 
                                            <span class="font-medium" x-text="Math.min(currentPage * 10, totalItems)"></span> of 
                                            <span class="font-medium" x-text="totalItems"></span> results
                                        </p>
                                    </div>
                                    <div>
                                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                            <button 
                                                @click="prevPage()" 
                                                :disabled="currentPage === 1"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600"
                                            >
                                                <span class="sr-only">Previous</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                            
                                            <!-- Page numbers -->
                                            <template x-for="page in Array.from({length: Math.min(5, lastPage)}, (_, i) => i + 1)">
                                                <button 
                                                    @click="goToPage(page)"
                                                    :class="{'bg-blue-50 dark:bg-blue-900 border-blue-500 dark:border-blue-600 text-blue-600 dark:text-blue-300': currentPage === page, 'bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600': currentPage !== page}"
                                                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                                                    x-text="page"
                                                ></button>
                                            </template>
                                            
                                            <span x-show="lastPage > 5" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                ...
                                            </span>
                                            
                                            <button 
                                                @click="nextPage()" 
                                                :disabled="currentPage === lastPage"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600"
                                            >
                                                <span class="sr-only">Next</span>
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </nav>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    
                    <template x-if="!isLoading && items.length === 0 && searchQuery.length > 0">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No items found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try a different search term</p>
                        </div>
                    </template>
                    
                </div>
                
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
    </x-modal>

</div>

<script>

window.currencySymbol = @js($sales_order->currency->symbol ?? '₱');

function attachmentsHandler() {
    return {
        files: [],
        pickFiles(e) {
            const newOnes = Array.from(e.target.files).map(f => ({ file: f, label: '' }));
            this.files.push(...newOnes);
            this.syncFilesToInput();
        },
        removeFile(i) {
            this.files.splice(i, 1);
            this.syncFilesToInput();
        },
        syncFilesToInput() {
            const dt = new DataTransfer();
            this.files.forEach(f => dt.items.add(f.file));
            this.$refs.fileInput.files = dt.files;
        },
        appendLabelsToForm(form) {
            this.files.forEach((entry, idx) => {
                form.append(`file_labels[${idx}]`, entry.label);
            });
        },
        init() {
            window.addEventListener('save-draft', (e) => {
                this.appendLabelsToForm(e.detail.formData);
            });
        }
    }
}


function commentSection(initialComments, salesOrderId) {
    return {
        comments    : [...initialComments].sort((a,b)=>
                      new Date(a.created_at) - new Date(b.created_at)), // oldest→newest
        newComment  : '',
        isLoading   : false,
        error       : null,
        salesOrderId: salesOrderId,

        formatDate(dateString) {
            return new Date(dateString).toLocaleString('en-US',{
                year:'numeric', month:'short', day:'numeric',
                hour:'2-digit', minute:'2-digit'
            });
        },

        async addComment() {
            if (!this.newComment.trim() || this.isLoading) return;

            this.isLoading = true;
            this.error     = null;

            try {
                const response = await fetch('{{ route('sales-order.comments.store') }}', {
                    method : 'POST',
                    headers: {
                        'Content-Type' : 'application/json',
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                        'Accept'       : 'application/json'
                    },
                    body: JSON.stringify({
                        comment       : this.newComment,
                        sales_order_id: this.salesOrderId
                    })
                });

                if (!response.ok) {
                    const err = await response.json();
                    throw new Error(err.message || 'Failed to save comment');
                }

                const saved = await response.json();

                /* ↓↓↓ add to bottom, not top */
                this.comments.push(saved);

                /* auto-scroll to bottom */
                this.$nextTick(() => {
                    const list = this.$root.querySelector('[x-ref=commentList]');
                    if (list) list.scrollTop = list.scrollHeight;
                });

                this.newComment = '';
            } catch (e) {
                this.error = e.message || 'Error saving comment';
                console.error(e);
            } finally {
                this.isSubmitting = false;
                this.isLoading    = false;
            }
        }
    }
}

</script>
<script>
document.addEventListener('alpine:init', () => {

    Alpine.store('order', {
        tax_status: @json($salesOrder->tax_status ?? '')
    });

        Alpine.data('customerLookup', (initialCustomer = null, locked = false) => ({
        query: initialCustomer?.name ?? '',
        chosen: initialCustomer,
        results: [],
        showResults: false,
        isLoading: false,
        error: null,
        locked,

        init() {
            // Always load SO's tax_status first
            let soTaxStatus = @js(old('tax_status', $sales_order->tax_status ?? null));

            if (soTaxStatus) {
                this.$store.order.tax_status = soTaxStatus;
            } else if (this.chosen?.tax_status) {
                // Only fallback to customer's tax_status if SO has none
                this.$store.order.tax_status = this.chosen.tax_status;
            }
        },

        async search() {
            if (this.locked || this.query.trim().length < 2) {
                this.showResults = false;
                return;
            }

            this.isLoading = true;
            this.results = [];
            this.error = null;

            try {
                const res = await fetch(`/api/customers?q=${encodeURIComponent(this.query)}`);
                if (!res.ok) throw new Error(`Server returned ${res.status}`);
                const data = await res.json();
                if (!Array.isArray(data)) throw new Error('Invalid response format');

                this.results = data;
                this.showResults = true;
            } catch (err) {
                console.error('Customer search failed:', err);
                this.error = 'Failed to search customers.';
                this.showResults = false;
            } finally {
                this.isLoading = false;
            }
        },

        pickCustomer(cust) {
            if (this.locked || !cust || typeof cust !== 'object') return;

            this.chosen = cust;
            this.query = cust.name || '';
            this.showResults = false;
            this.$dispatch('customer-selected', cust);

            if (cust.tax_status) {
                this.$store.order.tax_status = cust.tax_status;
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
        searchField: 'all',
        customName: '',
        customDescription: '',
        customUnit: '',
        
        init() {
            // Initialize with first page if no search query
            this.searchItems();
            // if (this.searchQuery.length > 0) {
            //     this.searchItems();
            // }
        },
        
        async searchItems(resetPage = true) {
            if (resetPage) {
                this.currentPage = 1;
            }
            
    
            this.isLoading = true;
            
            try {
                // const response = await fetch(`/items/search?q=${encodeURIComponent(this.searchQuery)}&page=${this.currentPage}`);
                const response = await fetch(`/items/search?q=${encodeURIComponent(this.searchQuery)}&page=${this.currentPage}&field=${this.searchField}`);

                const data = await response.json();
                
                this.items = data.data;
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
                this.totalItems = data.total;
            } catch (error) {
                console.error('Error searching items:', error);
                this.items = [];
                this.currentPage = 1;
                this.lastPage = 1;
                this.totalItems = 0;
            } finally {
                this.isLoading = false;
            }
        },
        
        addItem(item) {
            console.log('Adding item:', item);
            this.$dispatch('item-added', {
                item_id: item.id ?? null,
                name: item.name,
                description: item.description,
                unit_id: item.unit_id, 
                quantity: 1,
                unit_price: item.price || 0,
                pe_remarks: '',
                notes: ''
            });
            
            // this.$dispatch('close');
            this.searchQuery = '';
            // this.items = [];
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
        }
    }));

    Alpine.data('itemsTable', (params) => ({
    // Add this at the very top
    currencySymbol: window.currencySymbol || '₱', // fallback if window.currencySymbol is not set

    initialRows: params.initialRows || [],
    rows: [],
    nextLocalId: 0,

    init() {
        if (this.initialRows.length > 0) {
            this.rows = this.initialRows.map((row, i) => {
                return {
                    ...row,
                    local_uid: typeof row.local_uid === 'number' ? row.local_uid : i
                };
            });

            this.nextLocalId = Math.max(...this.rows.map(r => r.local_uid ?? 0)) + 1;
        }
    },

    receiveItems(items) {
        const newItems = Array.isArray(items) ? items : [items];
        newItems.forEach(item => {
            const exists = this.rows.some(row =>
                (row.item_id && row.item_id === item.item_id) ||
                (row.pricing_item_id && row.pricing_item_id === item.pricing_item_id)
            );
            if (!exists) {
                this.rows.push({
                    ...item,
                    local_uid: this.nextLocalId++
                });
            }
        });
    },

    deleteItem(row, index) {
        if (row.pricing_item_id) {
            this.rows[index]._delete = true;
        } else {
            this.rows.splice(index, 1);
        }
    },

    hiddenFields(row) {
        const prefix = row.pricing_item_id ? `items[${row.pricing_item_id}]` : `new_items[${row.local_uid}]`;
        return [
            [`${prefix}[item_id]`, row.item_id],
            [`${prefix}[name]`, row.name],
            [`${prefix}[description]`, row.description],
            [`${prefix}[quantity]`, row.quantity],
            [`${prefix}[unit_price]`, row.unit_price],
            [`${prefix}[unit_id]`, row.unit_id],
            [`${prefix}[_delete]`, row._delete ? 1 : 0]
        ];
    },

    formatCurrency(value) {
        return `${this.currencySymbol}${Number(value || 0).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    },

    calculateSubtotal(row) {
        return (row.quantity || 1) * (row.unit_price || 0);
    },

    subtotal() {
        const rawSubtotal = this.rows.reduce((sum, row) => {
            const qty = Number(row.quantity) || 0;
            const price = Number(row.unit_price) || 0;
            return sum + (qty * price);
        }, 0);

        const taxStatus = this.$store.order.tax_status;

        if (taxStatus === 'VAT Inclusive') {
            return rawSubtotal / 1.12;
        }

        return rawSubtotal;
    },

    vat() {
        const taxStatus = this.$store.order.tax_status;

        if (taxStatus === 'Zero Rated') {
            return 0;
        }
        if (taxStatus === 'VAT Inclusive') {
            const gross = this.rows.reduce((sum, row) => {
                const qty = Number(row.quantity) || 0;
                const price = Number(row.unit_price) || 0;
                return sum + (qty * price);
            }, 0);
            return gross - (gross / 1.12);
        }
        return 0;
    },

    total() {
        const taxStatus = this.$store.order.tax_status;

        if (taxStatus === 'VAT Inclusive') {
            return this.rows.reduce((sum, row) => {
                const qty = Number(row.quantity) || 0;
                const price = Number(row.unit_price) || 0;
                return sum + (qty * price);
            }, 0);
        }
        if (taxStatus === 'Zero Rated') {
            return this.subtotal(); 
        }
        return this.subtotal();
    }
}));


    
        Alpine.data('soApproval', ({ soID }) => ({
        isSubmitting: false,

        async submitSOApproval() {
            if (!soID) return;

            // ✅ Grab values directly from your form inputs
            const poNo = document.querySelector('#po_no')?.value?.trim();
            const deliveryDate = document.querySelector('[name="delivery_date"]')?.value?.trim();

            // ✅ Validation: require both fields
            if (!poNo || !deliveryDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Missing Required Fields',
                    text: 'Please enter both P.O. Number and Delivery Date before submitting.',
                    confirmButtonColor: '#f59e0b'
                });
                return; // stop submission
            }

            const confirmResult = await Swal.fire({
                title: 'Submit for Approval?',
                text: 'Are you sure you want to submit this Sales Order for approval?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Yes, submit it',
                cancelButtonText: 'Cancel'
            });

            if (!confirmResult.isConfirmed) return;

            this.isSubmitting = true;

            try {
                const response = await fetch(`/sales-order/${soID}/submit`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Submission failed');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Submitted!',
                    text: data.message || 'Sales Order submitted successfully.',
                    confirmButtonColor: '#2563eb'
                });

                // Redirect after success
                window.location.href = data.redirect;

            } catch (err) {
                console.error('❌ Submission error:', err);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Something went wrong.',
                    confirmButtonColor: '#ef4444'
                });
            } finally {
                this.isSubmitting = false;
            }
        }
    }));

 
Alpine.data('saveDraftHandler', () => ({
    isSaving: false, // Track loading state

    async confirmSave() {
        const result = await Swal.fire({
            title: 'Save as Draft?',
            text: 'Do you want to save this Sales Order as draft?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Save',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#16a34a',
            cancelButtonColor: '#6b7280'
        });

        if (!result.isConfirmed) return;

        this.isSaving = true; // Start loading

        const $form = this.$el;
        const formData = new FormData($form);

        // Let attachmentsHandler append labels
        window.dispatchEvent(new CustomEvent('save-draft', { detail: { formData } }));

        try {
            const response = await fetch($form.action, {
                method: $form.method,
                body: formData
            });

            if (response.ok) {
                // Optional: show a success toast instead of full reload
                Swal.fire({
                    icon: 'success',
                    title: 'Draft saved!',
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    window.location.reload(); // or update UI dynamically
                });
            } else {
                throw new Error('Server returned an error');
            }
        } catch (error) {
            Swal.fire('Error', 'Failed to save draft', 'error');
            console.error(error);
        } finally {
            this.isSaving = false; // Stop loading
        }
    }
}));

});
</script>