@php
    $editing = isset($pricing_estimate) && $pricing_estimate->exists;
    $initialCustomer = $editing && $pricing_estimate->customer
        ? $pricing_estimate->customer->toJson()
        : 'null';

    $initialRows = [];

    if(old('new_items')) {
        foreach(old('new_items') as $uid => $item) {
            $initialRows[] = [
                'local_uid' => $uid,
                'item_id' => $item['item_id'] ?? null,
                'name' => $item['name'] ?? '',
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'unit_id'     => $item['unit_id'] ?? null,
                'pe_remarks' => $item['pe_remarks'] ?? '',
                'notes' => $item['notes'] ?? '',
            ];
        }
    } elseif($editing) {
        $initialRows = $pricing_estimate->pe_items->map(function($item) {
            return [
                'pricing_item_id' => $item->id,
                'item_id' => $item->item_id,
                'name' => $item->item_name,
                'description' => $item->item_description,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'unit_id'         => $item->unit_id,
                'pe_remarks' => $item->pe_remarks,
                'notes' => $item->notes,
            ];
        })->toArray();
    }
    $vatOptions = config('selectOptions.vat_options'); 
    $selectedVat = old('vat', $pricing_estimate->vat);
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
<div x-data="pricingEstimateForm()">
    <form
        class="space-y-6"
        method="POST"
        action="{{ $editing ? route('pricing-estimates.update', $pricing_estimate->id) : route('pricing-estimates.store') }}"
        x-ref="form"
        enctype="multipart/form-data"
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
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full transition-colors duration-200">
        <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-200 dark:border-gray-600">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Customer Details</h2>
            <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-100 text-xs font-medium px-2.5 py-1 rounded">P.E NO: #{{$pe_no}}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Personal Info -->
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Name</p>
                    <p class="text-gray-900 dark:text-gray-100" x-text="chosen ? chosen.name : '--'">--</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Fax No:</p>
                    <p class="text-gray-900 dark:text-gray-100" x-text="chosen ? chosen.telefax : '--'">--</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Telephone No:</p>
                    <p class="text-gray-900 dark:text-gray-100" x-text="chosen ? chosen.telno : '--'">--</p>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="space-y-3">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</p>
                    <p class="text-gray-900 dark:text-gray-100" x-text="chosen ? chosen.email : '--'">--</p>
                </div>
                
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Address</p>
                    <p class="text-gray-900 dark:text-gray-100" x-text="chosen ? chosen.address : '--'">--</p>
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

                        @foreach(config('selectOptions.terms') as $term)
                            <option value="{{ $term }}" {{ old('terms', $pricing_estimate->terms) === $term ? 'selected' : '' }}>
                                {{ $term }}
                            </option>
                        @endforeach

                    </select>
                    <label for="sr_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Sales Representative
                    </label>

                    <select name="sr_id" 
                            id="sr_id"
                            class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                {{ old('sr_id', $pricing_estimate->sr_id ?? auth()->id()) == $user->id ? 'selected' : '' }}>
                                {{ $user->employee->full_name ?? $user->username }}
                            </option>
                        @endforeach
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
                        @foreach($vatOptions as $key => $label)
                            <option value="{{ $key }}" {{ $selectedVat == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
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
                        {{-- <div class="relative">
                            <input type="text"
                                placeholder="{{ $placeholder ?? 'Search items...' }}"
                                wire:model.live="search"
                                class="w-full md:w-64 pl-10 pr-4 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div> --}}
                        {{-- @if($pricing_estimate->pe_status == 'draft') --}}
                        <button type="button"
                                @click="$dispatch('open-modal', 'item-picker')"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                    text-white bg-blue-600 hover:bg-blue-700 focus:ring-2 focus:ring-blue-500
                                    transition-colors duration-200">
                            <i class="fas fa-plus"></i>
                            Add Items
                        </button>
                        {{-- @endif --}}
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
                                <x-table-column field="item_name">Item</x-table-column>
                                <x-table-column field="quantity"  class="text-center">Quantity</x-table-column>
                                {{-- <x-table-column field="unit_price">Unit Price</x-table-column> --}}
                                @if($pricing_estimate->pe_status == 'draft')
                                <x-table-column class="w-20 text-right">Actions</x-table-column>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="(row, idx) in rows" :key="row.pricing_item_id || row.local_uid">
                                 <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors" x-show="!row._delete" >
                                    <td x-text="idx + 1" class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400"></td>
                                        <td class="px-4 py-3">
                                            <!-- Name & Description -->
                                            <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
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
                                                {{-- <span x-text="row.unit_id"></span> --}}
                                                <br/>
                                                <!-- P.E Remarks -->
                                                <div x-show="row.pe_remarks" class="text-xs italic text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold not-italic text-gray-600 dark:text-gray-300">P.E Remarks:</span>
                                                    <span x-text="row.pe_remarks"></span>
                                                </div>

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
                                    <td class="px-2 py-2 text-center">
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
                                    </td>
                                    {{-- <td class="px-4 py-3 text-right" x-text="formatCurrency(row.unit_price)"></td> --}}
                                    @if($pricing_estimate->pe_status == 'draft')
                                    <td class="px-4 py-3 text-center space-x-2">
                                        <button type="button" 
                                                @click="deleteItem(row, idx)"
                                                class="text-red-500 hover:text-red-700 transition-colors"
                                                title="Remove item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                    @endif
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
                        const newOnes = Array.from(e.target.files).map(f => ({ file: f, label: '' }));
                        this.files = [...this.files, ...newOnes];
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
            <div class="flex justify-between items-center mt-6">

                {{-- Left Side Buttons --}}
                <div class="flex space-x-4">
                    <a href="{{ route('pricing-estimates.index') }}"
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                            text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                            border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                            focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Back to List
                    </a>

                    @if(!in_array($pricing_estimate->pe_status, ['pending', 'approved']))
                        <button type="button"
                                @click="submitDraft"
                                :disabled="isSubmitting"
                                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                    bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <span x-show="!isSubmitting">{{ $editing ? 'Save Draft' : 'Save Draft' }}</span>
                            <span x-show="isSubmitting">Processing...</span>
                        </button>
                    @endif
                </div>

                {{-- Right Side Button (Submit for Approval) --}}
                @if($editing && !in_array($pricing_estimate->pe_status, ['pending', 'approved']))
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

            </div>
        @endhasrole


    </form>


</div>
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
            <button
                type="button"
                @click="saveRemarks"
                :disabled="isSaving"
                class="px-4 py-2 bg-blue-600 text-white rounded flex items-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg x-show="isSaving" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span x-text="isSaving ? 'Saving...' : 'Save'"></span>
            </button>
        </div>
    </div>
</x-modal>
<x-modal name="item-picker" maxWidth="6xl">
    <div x-data="itemSearch()" class="bg-white dark:bg-gray-800 p-6 max-h-[80vh] overflow-y-auto">
        <div class="border-b border-gray-300 dark:border-gray-600 pt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Add Custom Item</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item Name *</label>
                    <input 
                        type="text" 
                        x-model="customName" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2"
                        placeholder="Enter item name"
                    >
                </div>
                
                <div class="space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Item Unit</label>
                    <select 
                        x-model="customUnit" 
                        {{-- class="w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" --}}
                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
                        <option disabled selected value="">-- Select unit --</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </select>

                    {{-- <input 
                        type="text" 
                        x-model="customUnit" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2"
                        placeholder="e.g., kg, lb, each"
                    > --}}
                </div>
                
                <div class="sm:col-span-2 space-y-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea 
                        x-model="customDescription" 
                        rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2"
                        placeholder="Optional description"
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 mb-3 flex justify-end">
                <button 
                    @click="addCustomItem"
                    :disabled="!customName"
                    class="inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Add Custom Item
                </button>
            </div>
        </div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Add Items</h2>
        
        <!-- Search input -->
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-1/3">
                <select x-model="searchField" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-200 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
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
            
            {{-- <template x-if="!isLoading && searchQuery.length === 0">
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Search for items</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter an item number or name to begin searching</p>
                </div>
            </template> --}}
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


<script>
document.addEventListener('alpine:init', () => {
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
        isSaving: false,

        init() {
            this.currentItem = { pe_remarks: '', pricing_item_id: null };
            window.addEventListener('set-remarks-data', (e) => {
                this.currentItem = e.detail;
            });
        },

        async saveRemarks() {
            if (!this.currentItem?.pricing_item_id) return;

            this.isSaving = true;

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

                window.dispatchEvent(new CustomEvent('item-updated', { detail: data }));
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: 'Remarks updated successfully!',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });

                this.$dispatch('close');
            } catch (error) {
                console.error('Failed to save remarks:', error);
                // alert(error.message);
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: error.message || 'Failed to save remarks',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                    },
                });

            } finally {
                this.isSaving = false;
            }
        }
    }));

    Alpine.data('peApproval', ({ estimateId }) => ({
        isSubmitting: false,

        async submitApproval() {
            if (!estimateId) return;

            const confirm = await Swal.fire({
                title: 'Submit for Approval?',
                text: 'If you made changes, please save draft first. Are you sure you want to submit this Pricing Estimate? Once submitted, it will no longer be editable.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Submit',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'bg-blue-600 text-white px-4 py-2 rounded mr-2',
                    cancelButton: 'bg-gray-200 text-gray-700 px-4 py-2 rounded',
                },
                buttonsStyling: false
            });

            if (!confirm.isConfirmed) return;

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

                await Swal.fire({
                    title: 'Success',
                    text: data.message || 'Submitted successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'bg-blue-600 text-white px-4 py-2 rounded',
                    },
                    buttonsStyling: false
                });

                // window.location.reload();
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    Swal.fire('Submitted!', 'Your pricing estimate has been submitted.', 'success');
                    window.location.reload();
                }
            } catch (err) {
                console.error('❌ Failed to submit:', err);
                await Swal.fire({
                    title: 'Error',
                    text: err.message,
                    icon: 'error',
                    confirmButtonText: 'Close',
                    customClass: {
                        confirmButton: 'bg-red-600 text-white px-4 py-2 rounded',
                    },
                    buttonsStyling: false
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
            // Swal.fire({
            //     toast: true,
            //     icon: 'success',
            //     title: 'Item added successfully!',
            //     position: 'top-end',
            //     showConfirmButton: false,
            //     timer: 3000,
            //     timerProgressBar: true,
            //     customClass: {
            //         popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
            //     },
            // });
            // this.$dispatch('close');
            this.searchQuery = '';
            // this.items = [];
        },
       addCustomItem() {
            if (!this.customName || !this.customDescription || !this.customUnit) {
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: 'Please fill in all required fields',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'bg-red-50 text-red-800 dark:bg-red-900 dark:text-red-200',
                    },
                });
                return;
            }

            this.$dispatch('item-added', {
                item_id: null,
                name: this.customName,
                description: this.customDescription,
                unit_id: this.customUnit,
                quantity: 1,
                unit_price: 0,
                pe_remarks: '',
                notes: ''
            });

            // Swal.fire({
            //     toast: true,
            //     icon: 'success',
            //     title: 'Item added successfully!',
            //     position: 'top-end',
            //     showConfirmButton: false,
            //     timer: 3000,
            //     timerProgressBar: true,
            //     customClass: {
            //         popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
            //     },
            // });

            // Reset fields
            this.customName = '';
            this.customDescription = '';
            this.customUnit = '';
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
    
    Alpine.data('itemsTable', ({ initialRows = [] }) => ({
        rows: initialRows,
        nextLocalId: 0,
        
        init() {
            // Initialize with existing items if editing
            if (initialRows.length > 0) {
                this.rows = initialRows;
                this.nextLocalId = Math.max(...initialRows.map(r => r.local_uid || 0)) + 1;
            }
            window.addEventListener('item-updated', (e) => {
                const updated = e.detail;
                const index = this.rows.findIndex(r => r.pricing_item_id === updated.pricing_item_id);
                if (index !== -1) {
                    this.rows[index].pe_remarks = updated.pe_remarks;

                    // ✅ Trigger Alpine reactivity
                    this.rows = JSON.parse(JSON.stringify(this.rows));
                }
            });
            
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
                } else {
                    Swal.fire({
                        toast: true,
                        icon: 'error',
                        title: `Item "${item.name}" is already added.`,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                        },
                    });
                }
            });
        },
                
        deleteItem(row, index) {
            const itemName = row.name || 'Unnamed Item';

            if (row.pricing_item_id) {
                // For existing items (edit mode), mark as deleted
                this.rows[index]._delete = true;
            } else {
                // For new items (store mode), remove directly
                this.rows.splice(index, 1);
            }


            Swal.fire({
                toast: true,
                icon: 'warning',
                title: `Item "${itemName}" removed.`,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white',
                },
            });
        },

        
        openRemarksModal(row) {
            // alert('Open');

            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'pe-remarks' }));

            window.dispatchEvent(new CustomEvent('set-remarks-data', {
                detail: {
                    pricing_item_id: row.pricing_item_id,
                    pe_remarks: row.pe_remarks
                }
            }));
        },


        
        openPriceModal(row) {
            this.$dispatch('open-modal', {
                name: 'price-modifier',
                data: {
                    pricing_item_id: row.pricing_item_id,
                    unit_price: row.unit_price,
                    discount: row.discount || 0,
                    discount_type: row.discount_type || 'percentage'
                }
            });
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
                [`${prefix}[pe_remarks]`, row.pe_remarks || ''],
                [`${prefix}[notes]`, row.notes || ''],
                [`${prefix}[_delete]`, row._delete ? 1 : 0]
            ];
        },
        
        formatCurrency(value) {
            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD'
            }).format(value || 0);
        },
        
        calculateSubtotal(row) {
            return (row.quantity || 1) * (row.unit_price || 0);
        },
        
        subtotal() {
            return this.rows.reduce((sum, row) => sum + this.calculateSubtotal(row), 0);
        },
        
        vat() {
            return this.subtotal() * 0.12; // Assuming 12% VAT
        },
        
        total() {
            return this.subtotal() + this.vat();
        }
    }));
    
    Alpine.data('pricingEstimateForm', () => ({
        isSubmitting: false,

        async submitForm() {
            this.isSubmitting = true;
            this.$refs.form.submit();
        },

        async submitDraft() {
            const result = await Swal.fire({
                title: 'Save Draft?',
                text: 'Do you want to save this Pricing Estimate as a draft?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Save',
                cancelButtonText: 'Cancel',
                customClass: {
                    confirmButton: 'bg-green-600 text-white px-4 py-2 rounded mr-2 hover:bg-green-700',
                    cancelButton: 'bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300',
                },
                buttonsStyling: false
            });

            if (!result.isConfirmed) return;

            const draftInput = document.createElement('input');
            draftInput.type = 'hidden';
            draftInput.name = 'is_draft';
            draftInput.value = '1';
            this.$refs.form.appendChild(draftInput);

            this.submitForm();
        }
    }));


});
</script>
