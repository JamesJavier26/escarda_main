@php
    $editing = isset($pricing_estimate) && $pricing_estimate->exists;
    $initialCustomer = $editing && $pricing_estimate->customer
        ? $pricing_estimate->customer->toJson()
        : 'null';

    $initialRows = $initialRows ?? [];
@endphp

<div x-data="pricingEstimateForm()">
    <form
        class="space-y-6"
        method="POST"
        action="{{ $editing ? route('pricing-estimates.update', $pricing_estimate->id) : route('pricing-estimates.store') }}"
        x-ref="form"
        @submit.prevent="submitForm"
    >
        @csrf
        @if($editing) 
            @method('PUT')
        @endif

        <div x-data="customerLookup({{ $initialCustomer }}, {{ $editing ? 'true' : 'false' }})">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative">
                <div class="col-span-1 space-y-4">
                    <div class="relative mb-6" x-show="!locked">
                        <label for="customer_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Search Customer <span class="text-red-500">*</span>
                        </label>
                        <input type="hidden" name="pe_no" value="{{$pe_no}}"/>
                        {{-- visible search box --}}
                        <input  id="customer_search"
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

                        {{-- suggestion dropdown --}}
                        <template x-if="showResults">
                            <ul class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200
                                        max-h-60 overflow-y-auto">
                                <template x-for="cust in results" :key="cust.id">
                                    <li  @click="pickCustomer(cust)"
                                        class="px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 cursor-pointer">
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

            <div class="grid grid-cols-1 gap-6 w-full">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                    <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-100">Customer Details</h2>
                    <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded">P.E NO: #{{$pe_no}}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Personal Info -->
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Name</p>
                            <p class="text-gray-100" x-text="chosen ? chosen.name : '--'">--</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Fax No:</p>
                            <p class="text-gray-100" x-text="chosen ? chosen.telefax : '--'">--</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-400">Telephone No:</p>
                            <p class="text-gray-100" x-text="chosen ? chosen.telno : '--'">--</p>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-3">
                        <div>
                        <p class="text-sm font-medium text-gray-400">Email</p>
                        <p class="text-gray-100" x-text="chosen ? chosen.email : '--'">--</p>
                        </div>
                        
                        <div>
                        <p class="text-sm font-medium text-gray-400">Address</p>
                        <p class="text-gray-100" x-text="chosen ? chosen.address : '--'">--</p>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Other Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <x-forms.input 
                        label="P.R No:" 
                        name="pr_no" 
                        type="text" 
                        :value="old('pr_no', $pricing_estimate->pr_no)" 
                    />

                    <x-forms.input 
                        label="Attention:" 
                        name="attention" 
                        type="text" 
                        :value="old('attention', $pricing_estimate->attention)" 
                    />

                    <label for="terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Terms</label>
                    <select name="terms" 
                        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="90 Days" {{ old('terms', $pricing_estimate->terms) === '90 Days' ? 'selected' : '' }}>90 Days</option>
                        <option value="80 Days" {{ old('terms', $pricing_estimate->terms) === '80 Days' ? 'selected' : '' }}>80 Days</option>
                    </select>
                </div>

                <div class="space-y-4">
                    <x-forms.input 
                        label="Delivery Date" 
                        name="delivery_date" 
                        type="text" 
                        :value="old('delivery_date', $pricing_estimate->delivery_date)" 
                    />   

                    <label for="currency_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Currency</label>
                    <select name="currency_id" 
                        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @foreach ($currencies as $currency)
                            <option 
                                value="{{ $currency->id }}" 
                                {{ old('currency_id', $pricing_estimate->currency_id) == $currency->id ? 'selected' : '' }}>
                                {{ $currency->symbol }} — {{ $currency->name }}
                            </option>
                        @endforeach
                    </select>

                    <label for="vat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">VAT</label>
                    <select name="vat" 
                        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="1" {{ old('vat', $pricing_estimate->vat) == 1 ? 'selected' : '' }}>VAT Include</option>
                        <option value="2" {{ old('vat', $pricing_estimate->vat) == 2 ? 'selected' : '' }}>VAT Exclude</option>
                        <option value="3" {{ old('vat', $pricing_estimate->vat) == 3 ? 'selected' : '' }}>Zero (0) Rated</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Items Section - Updated with discount and price modification -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 flex items-center gap-2">
                        <i class="fas fa-list-ul text-blue-500"></i>
                        {{ $title ?? 'Items' }}
                    </h3>
                    <div class="relative flex items-center gap-3">
                        <div class="relative">
                            <input type="text"
                                placeholder="{{ $placeholder ?? 'Search items...' }}"
                                wire:model.live="search"
                                class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <button type="button"
                                @click="$dispatch('open-modal', 'item-picker')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                    text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500
                                    transition-colors duration-200">
                            <i class="fas fa-plus"></i>
                            Add Items
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-4 overflow-x-auto">
                <div
                    x-data="itemsTable({ initialRows: @js($initialRows) })"
                    x-init="init()"
                    x-on:item-added.window="receiveItems($event.detail)"
                    id="itemsTable"
                >
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <x-table-column field="item_number" class="w-16">#</x-table-column>
                                <x-table-column field="item_name">Item Name</x-table-column>
                                @if($editing)
                                <x-table-column field="quantity" class="w-32 text-right">Qty</x-table-column>
                                <x-table-column field="price" class="w-32 text-right">Unit Price</x-table-column>
                                {{-- <x-table-column field="subtotal" class="w-32 text-right">SubTotal</x-table-column> --}}
                                <x-table-column class="w-20 text-right">Actions</x-table-column>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, idx) in rows" :key="row.pricing_item_id || row.local_uid">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td x-text="idx + 1" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"></td>
                                    <td class="px-4 py-3">
                                        <!-- Name & Description -->
                                        <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                            <!-- Item Name -->
                                            {{-- <div x-text="row.name || 'Unnamed Item'" class="font-medium"></div>

                                            <!-- Description -->
                                            <template x-if="row.description">
                                                <div x-text="row.description" class="text-xs text-gray-500 dark:text-gray-400"></div>
                                            </template> --}}
                                            <!-- Item Name -->
                                            <input type="text"
                                                x-model="row.name"
                                                placeholder="Unnamed Item"
                                                class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />

                                            <!-- Description -->
                                            <textarea 
                                                x-model="row.description"
                                                placeholder="Item description"
                                                rows="2"
                                                class="mt-1 text-xs w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600">
                                            </textarea>
                                            <br/>
                                            <!-- P.E Remarks -->
                                            <template x-if="row.pe_remarks">
                                                <div class="text-xs italic text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold not-italic text-gray-600 dark:text-gray-300">P.E Remarks:</span>
                                                    <span x-text="row.pe_remarks"></span>
                                                </div>
                                            </template>

                                            <!-- Pricing Remarks -->
                                            <template x-if="row.notes">
                                                <div class="text-xs italic text-blue-600 dark:text-blue-400">
                                                    <span class="font-semibold not-italic text-blue-700 dark:text-blue-300">Pricing Remarks:</span>
                                                    <span x-text="row.notes"></span>
                                                </div>
                                            </template>
                                        </div>


                                        @if($editing)
                                        <button
                                            type="button"
                                            @click="openRemarksModal(row)"
                                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-plus mr-1 text-[0.65rem]"></i> Add/Edit Remarks
                                        </button>
                                        @endif
                                    </td>
                                    @if($editing)
                                    <td class="px-4 py-3 text-right" x-text="row.quantity"></td>
                                    <td class="px-4 py-3 text-right" x-text="formatCurrency(row.unit_price)"></td>
                                    {{-- <td x-text="formatCurrency(calculateSubtotal(row))" class="px-4 py-3 text-sm font-medium text-right"></td> --}}
                                    <td class="px-4 py-3 text-center space-x-2">
                                        @hasrole('super-admin|admin|operation-manager')
                                        {{-- <button type="button" 
                                               
                                                class="text-blue-500 hover:text-blue-700 transition-colors"
                                                title="Add Offer">
                                            <i class="fas fa-plus"></i>
                                        </button> --}}
                                        <button type="button" 
                                                @click="openPriceModal(row)"
                                                class="text-blue-500 hover:text-blue-700 transition-colors"
                                                title="Modify price">
                                            <i class="fas fa-calculator"></i>
                                        </button>
                                        @endhasrole
                                        <button type="button" 
                                                @click="deleteItem(row, idx)"
                                                class="text-red-500 hover:text-red-700 transition-colors"
                                                title="Remove item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                    @endif

                                    <!-- Hidden inputs for form submission -->
                                    <template x-for="[name, value] in hiddenFields(row)" :key="name">
                                        <input type="hidden" :name="name" :value="value">
                                    </template>
                                </tr>
                            </template>

                            <tr x-show="rows.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center gap-2 mt-2 mb-2">
                                        <i class="fas fa-box-open text-2xl text-gray-300"></i>
                                        <span>No items added yet</span>
                                        <button type="button"
                                                @click="$dispatch('open-modal', 'item-picker')"
                                                class="text-blue-500 hover:text-blue-700 text-sm font-medium mt-2">
                                            <i class="fas fa-plus mr-1"></i> Add your first item
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        @if($editing)
                        <tfoot x-show="rows.length > 0" class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                            {{-- <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal:</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white" x-text="formatCurrency(subtotal())"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">VAT (12%):</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white" x-text="formatCurrency(vat())"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white" x-text="formatCurrency(total())"></td>
                                <td></td>
                            </tr> --}}
                            <tr>
                                <td colspan="5">
                                    <div class="px-4 pb-4 flex justify-start mt-5" x-show="rows.length > 0">
                                        <div class="space-x-3">
                                            @php
                                                $price_estimate_id = $editing ? $pricing_estimate->id : null;
                                            @endphp

                                            <a href="{{ route('quotations.generate', $price_estimate_id) }}" 
                                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-eye mr-2"></i> View Quotation
                                            </a>
                                            
                                            {{-- <a href="{{ route('reports.quotations', ['pe_no' => '7064']) }}" 
                                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                <i class="fas fa-save mr-2"></i> Print Quotation
                                            </a> --}}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Remarks & Attachments -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Remarks</h2>
                <textarea name="pe_remarks" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                >{{ old('pe_remarks', $pricing_estimate->pe_remarks) }}</textarea>

            </div>

            <div x-data="{
                    files: [],
                    pickFiles(e) {
                        const newOnes = Array.from(e.target.files).map(f => ({file:f,label:''}));
                        this.files = [...this.files, ...newOnes];
                    },
                    removeFile(i){
                        this.files.splice(i,1);
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f.file));
                        $refs.fileInput.files = dt.files;
                    }
                }"
                class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">

                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Attachments</h2>

                <input type="file" name="pe_attachment[]" multiple x-ref="fileInput" class="hidden" @change="pickFiles">

                <button type="button" @click="$refs.fileInput.click()"
                    class="px-4 py-2 rounded-md bg-blue-600 text-white text-sm font-medium hover:bg-blue-700">
                    <i class="fas fa-paperclip mr-1"></i> Select files
                </button>

                {{-- existing attachments (edit) --}}
                @if($editing && $pricing_estimate->attachments->count())
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-4">
                        @foreach($pricing_estimate->attachments as $file)
                            {{-- each file gets its own Alpine scope --}}
                            <div x-data="{ keep: true }"
                                x-show="keep"
                                class="relative border border-gray-300 dark:border-gray-600 p-3 rounded-md">

                                <!-- hidden field tells Laravel which attachments to keep -->
                                <input type="hidden"
                                    name="keep_attachments[]"
                                    :value="keep ? '{{ $file->id }}' : ''"
                                    x-show="keep">

                                <!-- remove button -->
                                <button type="button"
                                        @click="keep = false"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full
                                            w-6 h-6 flex items-center justify-center hover:bg-red-700"
                                        title="Remove this file">
                                    <i class="fas fa-times text-xs"></i>
                                </button>

                                {{-- preview --}}
                                @if(\Illuminate\Support\Str::startsWith($file->metadata['mime_type'] ?? '', 'image/'))
                                    <img src="{{ Storage::url($file->metadata['path']) }}" class="w-full h-40 object-contain rounded">
                                @elseif(($file->metadata['mime_type'] ?? '') === 'application/pdf')
                                    <iframe src="{{ Storage::url($file->metadata['path']) }}" class="w-full h-40 rounded"></iframe>
                                @else
                                    <div class="flex items-center justify-center h-40 bg-gray-100 dark:bg-gray-700 rounded">
                                        <i class="fas fa-file-alt text-4xl text-gray-400"></i>
                                    </div>
                                @endif

                                <p class="mt-2 text-xs truncate">
                                    {{ $file->metadata['original_name'] ?? basename($file->metadata['path']) }}
                                </p>

                                <!-- editable label -->
                                <input type="text"
                                    class="mt-2 w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600
                                            rounded dark:bg-gray-700 dark:text-gray-100"
                                    name="existing_file_labels[{{ $file->id }}]"
                                    value="{{ $file->label }}"
                                    placeholder="Enter label/description">
                            </div>
                        @endforeach

                    </div>
                @endif

                {{-- new uploads --}}
                <template x-if="files.length">
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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

                                <input type="text"
                                    class="mt-2 w-full px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded dark:bg-gray-700 dark:text-gray-100"
                                    x-model="entry.label"
                                    name="file_labels[]"
                                    placeholder="Enter label/description">
                            </div>
                        </template>
                    </div>
                </template>
            </div>

        </div>
        
        @hasrole('super-admin|admin|sales-representative')
        <div class="flex justify-end space-x-4">
            <a href="{{ route('pricing-estimates.index') }}"
               class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                      text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                      border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                Back to List
            </a>
                @if($pricing_estimate->pe_status <> 'pending')
                    <button type="button"
                            @click="submitDraft"
                            :disabled="isSubmitting"
                            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <span x-show="!isSubmitting">{{ $editing ? 'Save Draft' : 'Save Draft' }}</span>
                        <span x-show="isSubmitting">Processing...</span>
                    </button>
                
                @if($editing)
                    <div x-data="peApproval({ estimateId: {{ $pricing_estimate->id }} })">
                        <button type="button"
                                @click="submitApproval"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                    bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span x-show="!isSubmitting">Submit For Approval</span>
                            <span x-show="isSubmitting">Submitting...</span>
                        </button>
                    </div>

                @endif
                @endif
        </div>
        @endhasrole()
    </form>

    <!-- Price Modification Modal -->
    <x-modal name="price-modification" maxWidth="6xl">
        <div x-data="priceModification()" class="p-6">
            <h2 class="text-lg font-semibold mb-4">Price Modification</h2>
            
            <!-- Main Content Grid -->
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
                
                <!-- Middle Column - Calculation Fields -->
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-md font-medium mb-3 text-gray-700 dark:text-gray-300">Calculation</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Factor Rate</label>
                                <div class="relative">
                                    <input type="number" step="0.0001" x-model="factorRate" @change="calculateUnitPrice"
                                        class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">SUR</label>
                                <input type="number" step="0.01" x-model="sum_charge" @change="calculateUnitPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Quantity</label>
                                <input type="number" step="1" min="1" x-model="quantity" @change="calculateUnitPrice"
                                    class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Unit Price</label>
                                <input type="number" x-model="unitPrice" readonly
                                    class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md border font-bold text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                    <hr/>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Override Unit Price</label>
                                <input type="number"
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
                
                <!-- Right Column - Discount Section -->
                <div class="md:col-span-1 space-y-4">
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-md font-medium mb-3 text-gray-700 dark:text-gray-300">Discount</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Discount Type</label>
                                <select x-model="discountType" 
                                        class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700">
                                    <option value="percentage">Percentage Discount</option>
                                    <option value="fixed">Fixed Amount Discount</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                                    <span x-text="discountType === 'percentage' ? 'Discount Percentage' : 'Discount Amount'"></span>
                                </label>
                                <div class="relative">
                                    <input type="number" x-model="discountAmount" step="0.01" min="0"
                                        class="w-full pl-3 pr-8 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <div x-show="discountType === 'percentage'" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500">%</span>
                                    </div>
                                    <div x-show="discountType === 'fixed'" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-peso-sign text-gray-500"></i>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- <div class="bg-blue-50 dark:bg-blue-900/30 p-3 rounded-md border border-blue-100 dark:border-blue-900">
                                <p class="text-sm text-gray-600 dark:text-gray-300 flex justify-between">
                                    <span>Original Price:</span>
                                    <span class="font-medium">₱<span x-text="formatNumber(currentItem ? currentItem.unit_price : 0)"></span></span>
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-2 flex justify-between">
                                    <span>Discounted Price:</span>
                                    <span class="font-medium text-blue-600 dark:text-blue-400">₱<span x-text="formatNumber(calculateDiscountedPrice())"></span></span>
                                </p>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
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

    <x-modal name="pe-remarks" maxWidth="md"> 
        <div class="p-6" x-data="peRemarks()">
            <h2 class="text-lg font-semibold mb-4">P.E Remarks</h2>

            <textarea
                x-model="currentItem.pe_remarks"
                class="w-full h-32 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded shadow-sm dark:bg-gray-700 dark:text-gray-100"
                placeholder="Enter P.E remarks...">
            </textarea>

            <div class="flex justify-end mt-4 space-x-3">
                <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">Cancel</button>
                <button type="button" @click="saveRemarks" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
            </div>
        </div>
    </x-modal>

    <x-modal name="item-picker" class="max-w-5xl">
        <div x-data="itemPicker()" class="flex flex-col h-full">
            <div class="p-4 bg-gray-100 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700">
                <h2 class="text-lg font-semibold">Choose Items</h2>
                <div class="mt-4 flex">
                    <input x-model="searchTerm"
                        @input.debounce.400ms="fetchItems"
                        type="text"
                        placeholder="Search by name or number"
                        class="flex-1 px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600
                        bg-white dark:bg-gray-700 focus:ring-blue-500 focus:border-blue-500">
                    <button @click="fetchItems"
                            class="px-3 py-2 rounded-r-md bg-gray-200 dark:bg-gray-600">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="p-4 overflow-x-auto max-h-[70vh]">
                <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                        <tr class="text-left">
                            <th class="px-4 py-2 w-10">
                                <input type="checkbox" 
                                    @change="toggleAllItems($event)"
                                    :checked="picked.length === items.length && items.length > 0">
                            </th>
                            <th class="px-4 py-2">Item No.</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-2">
                                    <input type="checkbox" 
                                        :checked="isItemSelected(item)"
                                        @change="toggleItem(item)"
                                        @click.stop>
                                </td>
                                <td class="px-4 py-2" x-text="item.number"></td>
                                <td class="px-4 py-2" x-text="item.name"></td>
                                <td class="px-4 py-2" x-text="item.description"></td>
                            </tr>
                        </template>
                        <tr x-show="items.length === 0">
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">
                                No items found
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-gray-50 dark:bg-gray-800 border-t border-gray-300 dark:border-gray-700 flex justify-end space-x-3">
                <button type="button" 
                        @click="$dispatch('close')" 
                        class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">
                    Back to List
                </button>
                <button type="button" 
                        @click="addSelectedItems"
                        :disabled="picked.length === 0"
                        class="px-4 py-2 bg-blue-600 text-white rounded disabled:opacity-50">
                    Add Selected (<span x-text="picked.length"></span>)
                </button>
            </div>
        </div>
    </x-modal>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('peItems', { rows: [] });
    // Main form controller
    Alpine.data('pricingEstimateForm', () => ({
        isSubmitting: false,
        errors: {},
        successMessage: null,
        
        async submitForm() {
            this.isSubmitting = true;
            this.errors = {};
            this.successMessage = null;
            
            try {
                const formData = new FormData(this.$refs.form);
                const method = this.$refs.form.querySelector('input[name="_method"]')?.value || 'POST';
                
                // Add items data to formData
                const itemsData = Alpine.store('peItems').rows.map(row => ({
                    item_id: row.item_id,
                    quantity: row.quantity,
                    unit_id: row.unit_id,
                    unit_price: row.unit_price || 0,
                    price_factor: row.price_factor || {},
                    factor_rate: row.factor_rate || 0,
                    sum_charge: row.sum_charge || 0,
                    pe_remarks: row.pe_remarks || '',
                    notes: row.notes || ''
                }));
                
                formData.append('items', JSON.stringify(itemsData));
                
                const response = await fetch(this.$refs.form.action, {
                    method: method, // Use the correct method (PUT for update)
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422) {
                        this.errors = data.errors;
                        // Scroll to first error
                        const firstError = Object.keys(data.errors)[0];
                        document.querySelector(`[name="${firstError}"]`)?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    } else {
                        throw new Error(data.message || 'An error occurred');
                    }
                    return;
                }
                
                // Success - show message and optionally redirect
                this.successMessage = data.message || (method === 'POST' ? 'Pricing estimate created successfully!' : 'Pricing estimate updated successfully!');
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            } catch (error) {
                console.error('Save error:', error);
                alert(error.message || 'An error occurred while saving');
            } finally {
                this.isSubmitting = false;
            }
        },
        // Add this method to handle form submission for approval
        async submitDraft() {
            if (confirm('Are you sure you want to submit this pricing estimate for approval?')) {
                // Set a hidden field or add a parameter to indicate this is for approval
                const formData = new FormData(this.$refs.form);
                formData.append('submit_for_approval', 'true');
                
                this.isSubmitting = true;
                
                try {
                    const response = await fetch(this.$refs.form.action, {
                        method: this.$refs.form.method,
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.message || 'Submission failed');
                    }
                    
                    // Redirect to show page or index
                    window.location.href = data.redirect || '{{ route("pricing-estimates.index") }}';
                } catch (error) {
                    alert(error.message || 'Submission failed');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }));

    // Price modification modal
    Alpine.data('priceModification', () => ({
        currentItem: {
            unit_price: 0,
            quantity: 1,
            price_factor: {},
            factor_rate: 1.0,
            notes: ''
        },
        factors: { R: false, E: false, L: false, M: false, D: false },
        factorRates: { R: 0, E: 0, L: 0, M: 0, D: 0 },
        sum_charge: 0,
        factorRate: 1.0,
        quantity: 1,
        unitPrice: 0,
        notes: '',

        init() {
            window.addEventListener('set-price-data', (e) => {
                this.currentItem = e.detail;

                if (this.currentItem) {
                    let pf = {};

                    try {
                        pf = typeof this.currentItem.price_factor === 'string'
                            ? JSON.parse(this.currentItem.price_factor)
                            : (this.currentItem.price_factor || {});
                    } catch (e) {
                        console.warn('Failed to parse price_factor:', e);
                        pf = {};
                    }

                    Object.keys(this.factors).forEach(key => {
                        this.factors[key] = pf[key] !== undefined;
                        this.factorRates[key] = pf[key] || 0;
                    });

                    this.sum_charge = this.currentItem.sum_charge || 0;
                    this.factorRate = this.currentItem.factor_rate || 1.0;
                    this.quantity = this.currentItem.quantity || 1;
                    this.notes = this.currentItem.notes || '';
                    this.unitPrice = this.currentItem.unit_price || 0;

                    this.calculateUnitPrice();
                }
            });
        },
        
        calculateUnitPrice() {
            // Calculate sum of all factor rates (regardless of checkbox)
            let sumFactors = 0;
            Object.keys(this.factorRates).forEach(key => {
                sumFactors += parseFloat(this.factorRates[key]) || 0;
            });
            
            // Calculate unit price: [((R + E + L + M + D) * FR) + SUR] / QTY
            const numerator = (sumFactors * parseFloat(this.factorRate)) + parseFloat(this.sum_charge);
            this.unitPrice = numerator / parseFloat(this.quantity);
            this.unitPrice = parseFloat(this.unitPrice.toFixed(2));
        },

        
        applyPrice() {
            this.isSubmitting = true;
            const payload = {
                price_factor: this.factorRates,
                factor_rate: this.factorRate,
                sum_charge: this.sum_charge,
                quantity: this.quantity,
                unit_price: this.unitPrice,
                notes: this.notes,
            };
            console.log('➡ Sending update for item ID:', this.currentItem?.pricing_item_id);

            fetch(`/pricing-estimate-items/${this.currentItem.pricing_item_id}/update-unit-price`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            })
            .then(async response => {
                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch {
                    data = { message: text };
                }

                if (!response.ok) {
                    console.error('❌ Server response error:', {
                        status: response.status,
                        statusText: response.statusText,
                        response: data
                    });

                    alert(
                        `Update failed (${response.status}): ${
                            data?.message || 'Unknown error'
                        }`
                    );
                    return;
                }


                this.currentItem.quantity = data.quantity;
                this.currentItem.unit_price = data.unit_price;
                this.currentItem.price_factor = data.price_factor;
                this.currentItem.factor_rate = data.factor_rate;
                this.currentItem.sum_charge = data.sum_charge;
                this.currentItem.notes = data.notes;

                // Dispatch event to update parent table row (itemsTable)
                this.$dispatch('item-updated', data);
                this.$dispatch('close');
            })
            .catch(error => {
                console.error('Fetch error (likely network):', error);
                alert(`Failed to send request: ${error.message}`);
            })
            .finally(() => {
                this.isSubmitting = false; 
            });
        }


    }));

    Alpine.data('itemPicker', () => ({
        searchTerm: '',
        items: [],
        picked: [],
        isLoading: false,

        init() {
            this.fetchItems();
        },

        async fetchItems() {
            this.isLoading = true;
            try {
                const response = await fetch(`/api/items?q=${encodeURIComponent(this.searchTerm)}`);
                this.items = await response.json();
            } catch (error) {
                console.error('Error fetching items:', error);
            } finally {
                this.isLoading = false;
            }
        },

        isItemSelected(item) {
            return this.picked.some(p => p.id === item.id);
        },

        toggleItem(item) {
            if (this.isItemSelected(item)) {
                this.picked = this.picked.filter(p => p.id !== item.id);
            } else {
                this.picked.push({...item});
            }
        },

        toggleAllItems(event) {
            if (event.target.checked) {
                this.picked = this.items.map(item => ({...item}));
            } else {
                this.picked = [];
            }
        },

        addSelectedItems() {
            if (this.picked.length > 0) {
                this.$dispatch('item-added', this.picked);
                this.picked = [];
                this.$dispatch('close');
            }
        }
    }));


    Alpine.data('itemsTable', ({ initialRows = [] }) => ({
        rows: [],
        currentItem: null,
        init() {
            console.log(this.rows);
            // hydrate rows when the component is first attached
            this.rows = initialRows.map(item => ({
                local_uid: crypto.randomUUID?.() || `uid-${Date.now()}-${index}`,
                pricing_item_id: item.pricing_item_id,
                item_id:         item.item_id,
                number:       item.number,
                name:         item.name,
                description:  item.description,
                unit_id:      item.unit_id,
                quantity:     item.quantity     ?? 1,
                unit_price:   item.unit_price   ?? 0,
                price_factor: item.price_factor ?? {},
                factor_rate:  item.factor_rate  ?? 1.0,
                sum_charge:   item.sum_charge   ?? 0,
                pe_remarks:   item.pe_remarks      ?? '',
                notes:        item.notes        ?? ''
            }));
            window.addEventListener('item-updated', (e) => {
                const updatedItem = e.detail;
                const index = this.rows.findIndex(r => r.id === updatedItem.id);
                if (index !== -1) {
                    this.rows[index] = updatedItem;
                }
            });
        },
        
        receiveItems(selectedItems) {
            selectedItems.forEach(item => {
                if (!this.rows.some(r => r.id === item.id)) {
                    this.rows.push({
                        local_uid: crypto.randomUUID?.() || `uid-${Date.now()}-${Math.random()}`,
                        id: item.id,
                        number: item.number,
                        name: item.name,
                        description: item.description,
                        unit_id: item.unit_id,
                        quantity: 1,
                        unit_price: 0,
                        price_factor: {},
                        factor_rate: 1.0,
                        sum_charge: 0,
                        remarks: ''  
                    });
                }
            });
        },
        calculateSubtotal(row) {
            const subtotal = row.quantity * row.unit_price;
            let discount = 0;
            
            if (row.discount_type === 'percentage') {
                discount = subtotal * (row.discount_amount / 100);
            } else {
                discount = row.discount_amount * row.quantity;
            }
            
            // return subtotal - discount;
            return 0;
        },
        
        subtotal() {
            // return this.rows.reduce((sum, row) => sum + this.calculateSubtotal(row), 0);
            return 0;
        },
        
        vat() {
            // return this.subtotal() * 0.12;
            return 0
        },
        
        total() {
            // return this.subtotal() + this.vat();
            return 0;
        },
        
        updateRow(row) {
            row.sum_charge = this.calculateSubtotal(row);
        },
        
        openPriceModal(row) {
            this.$dispatch('open-modal', 'price-modification');
            console.log(' Modal Row Data:', row);
            window.dispatchEvent(new CustomEvent('set-price-data', { detail: row }));
        },
        
        openRemarksModal(row) {
            this.currentItem = row;
            this.$dispatch('open-modal', 'pe-remarks');
            window.dispatchEvent(new CustomEvent('set-remarks-data', { detail: row }));
        },
        async deleteItem(row, index) {
            if (!confirm('Are you sure you want to delete this item?')) return;

            if (!row.pricing_item_id) {
                this.rows.splice(index, 1);
                return;
            }

            try {
                const res = await fetch(`/pricing-estimate-items/${row.pricing_item_id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });

                if (!res.ok) throw new Error('Failed to delete');

                this.rows.splice(index, 1); // Remove from local UI
            } catch (err) {
                alert('Unable to delete item.');
                console.error(err);
            }
        },

        hiddenFields(row) {
            return [
                [`items[${row.id}][id]`, row.id],
                [`items[${row.id}][item_id]`, row.id],
                [`items[${row.id}][quantity]`, row.quantity],
                 [`items[${row.id}][unit_id]`,    row.unit_id],
                [`items[${row.id}][unit_price]`, row.unit_price],
                [`items[${row.id}][price_factor]`, JSON.stringify(row.price_factor)],
                [`items[${row.id}][factor_rate]`, row.factor_rate],
                [`items[${row.id}][sum_charge]`, row.sum_charge],
                // Add other fields as needed
            ];
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'PHP' // Change as needed
            }).format(value);
        }
    }));
    

    Alpine.data('customerLookup', (initialCustomer = null, locked = false) => ({
        query: initialCustomer?.name ?? '',
        chosen: initialCustomer,
        results: [],
        showResults: false,
        isLoading: false,
        error: null,
        locked,

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
        }
    }));

    Alpine.data('peRemarks', () => ({
        currentItem: {
            pe_remarks: '',
            pricing_item_id: null,
        },

        init() {
            window.addEventListener('set-remarks-data', (e) => {
                this.currentItem = e.detail;
            });
        },

        async saveRemarks() {
            if (!this.currentItem?.pricing_item_id) return;

            const payload = {
                pe_remarks: this.currentItem.pe_remarks
            };

            try {
                const response = await fetch(`/pricing-estimate-items/${this.currentItem.pricing_item_id}/update-remarks`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to update remarks');
                }

                // Update the current row with new remarks
                this.$dispatch('item-updated', data);
                this.$dispatch('close');

            } catch (error) {
                console.error('❌ Failed to save remarks:', error);
                alert(error.message);
            }
        }
    }));

    Alpine.data('peApproval', ({ estimateId }) => ({
        isSubmitting: false,

        async submitApproval() {
            if (!estimateId) return;

            if (!confirm('Submit this Pricing Estimate for approval?')) return;

            this.isSubmitting = true;

            try {
                const response = await fetch(`/pricing-estimates/${estimateId}/submit`, {
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

                // Reload or notify
                alert(data.message || 'Submitted successfully!');
                window.location.reload();

            } catch (err) {
                console.error('❌ Failed to submit:', err);
                alert(err.message);
            } finally {
                this.isSubmitting = false;
            }
        }
    }));


});
</script>
