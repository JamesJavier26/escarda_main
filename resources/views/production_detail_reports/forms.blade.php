@props(['report'])

@php
    $editing = isset($report) && $report->exists;
    $salang1 = $editing ? $report->salang_1 ?? [] : [];
    $salang2 = $editing ? $report->salang_2 ?? [] : [];
    $salang3 = $editing ? $report->salang_3 ?? [] : [];
    $salangs = [$salang1, $salang2, $salang3];

@endphp
<form method="POST" 
      action="{{ $editing ? route('production-detail-reports.update', $report->id) : route('production-detail-reports.store') }}"
      x-data="{
          isSubmitting: false,
          confirmSave($event) {
              Swal.fire({
                  title: 'Save Changes?',
                  text: 'Do you want to save the changes made to this report?',
                  icon: 'question',
                  showCancelButton: true,
                  confirmButtonColor: '#2563eb',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, Save'
              }).then((result) => {
                  if (result.isConfirmed) {
                      this.isSubmitting = true;
                      $event.target.closest('form').requestSubmit(); // ✅ use requestSubmit()
                  }
              });
          }
      }">
    @csrf
    @if ($editing)
        @method('PUT') {{-- ✅ must be inside form --}}
    @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        {{-- Job Order Selection --}}
        <div x-data="jobOrderSelect({{ $editing ? 'true' : 'false' }})" class="mb-6 relative">
            {{-- JO Search --}}
            <div x-show="!isEditMode">
                <label for="job_order_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Search Job Order <span class="text-red-500">*</span>
                </label>
                <div x-show="isEditMode">This only shows in edit mode</div>
                <input
                    id="job_order_search"
                    x-model="query"
                    @input.debounce.400ms="searchJO"
                    type="text"
                    placeholder="Search by JO number..."
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                        border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            {{-- Hidden fields --}}
            <input type="hidden" name="job_order_id" :value="selected?.id">
            <input type="hidden" name="jo_no" :value="selected?.jo_no">
            <input type="hidden" name="jo_item_id" :value="selectedJoItem">

            {{-- Suggestions Dropdown --}}
            <template x-if="showResults">
                <ul class="absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 max-h-60 overflow-y-auto">
                    <template x-for="jo in results" :key="jo.id">
                        <li @click="pickJO(jo)" class="px-4 py-2 hover:bg-blue-100 dark:hover:bg-gray-700 cursor-pointer">
                            <p class="font-medium" x-text="jo.jo_no"></p>
                            <p class="text-xs text-gray-500" x-text="'Customer: ' + (jo.customer_name ?? '--')"></p>
                        </li>
                    </template>
                    <li x-show="results.length === 0" class="px-4 py-2 text-sm text-gray-500">No matches</li>
                </ul>
            </template>

            {{-- JO Item Select --}}
            <div x-show="!isEditMode">
                <div class="mt-6" x-show="joItems.length > 0">
                    <label for="jo_item_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Item from Job Order <span class="text-red-500">*</span>
                    </label>

                    <select name="jo_item_id" id="jo_item_id"
                            x-model="selectedJoItem"
                            @change="fetchItemDetails(selectedJoItem)"
                            class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">

                        <template x-for="item in joItems" :key="item.id">
                            <option :value="item.id" x-text="'#' + item.item_name + ' - ' + item.item_desc"></option>
                        </template>
                    </select>
                </div>
            </div>

            {{-- Selected JO Preview --}}
            <div class="grid grid-cols-1 gap-6 w-full mt-6" x-show="selected">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                    <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Job Order Details</h2>
                        <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded"
                            x-text="selected ? 'JO No: #' + selected.jo_no : ''"></span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Customer</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.customer_name || '--'">--</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">SO No.</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.so_no || '--'">--</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">PO No.</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.po_no || '--'">--</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">PR No.</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.pr_no || '--'">--</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-medium text-gray-400">Sales Representative</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.sales_rep || '--'">--</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">Delivery Details</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.delivery_details || '--'">--</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-400">Remarks</p>
                                <p class="text-gray-900 dark:text-gray-100"" x-text="selected?.remarks || '--'">--</p>
                            </div>
                        </div>
                            {{-- Selected Item Preview --}}
                            <div class="col-span-2 mt-6 border-t border-gray-600 pt-4" x-show="isEditMode">
                                <p class="text-sm font-medium text-gray-400">Selected Item</p>
                                <p class="text-base text-gray-900 dark:text-gray-100"
                                x-text="selectedJoItemObj ? (selectedJoItemObj.item_name ?? selectedJoItemObj.name ?? '--') : '--'">
                                </p>
                            </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4" 
                            x-data="{ 
                                productionType: '{{ old('production_type', $report->production_type ?? '') }}', 
                                isLinear: {{ old('is_linear', $report->jobOrderItem?->is_linear ?? false) ? 'true' : 'false' }} 
                            }">

                            <!-- Production Type Label -->
                            <div class="col-span-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Production Type
                                </label>
                            </div>

                            <!-- Production Type Radios -->
                            <template x-for="type in ['old','new','trade']" :key="type">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="radio"
                                            name="production_type"
                                            :value="type"
                                            x-model="productionType">
                                        <span class="ml-2" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></span>
                                    </label>
                                </div>
                            </template>

                        <!-- Linear / Not Linear Label -->
                            <div class="col-span-3 mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Linear
                                </label>
                            </div>

                            <!-- Linear Radios -->
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio"
                                        name="is_linear"
                                        value="1"
                                        x-model="isLinear">
                                    <span class="ml-2">Yes</span>
                                </label>
                            </div>
                            <div>
                                <label class="inline-flex items-center">
                                    <input type="radio"
                                        name="is_linear"
                                        value="0"
                                        x-model="isLinear">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            <!-- First Row: Date, Press,-->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Date Production --}}
                <div>
                    <label for="date_production"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Date of Production <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="datetime-local"
                        name="date_production"
                        value="{{ old('date_production', $editing ? $report->date_production->format('Y-m-d\TH:i') : now()->format('Y-m-d\TH:i')) }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            readonly>
                </div>
                
                {{-- Press --}}
                <div>
                    <label for="press" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Press <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="press"
                        name="press"
                        value="{{ old('press', $editing ? $report->press : '') }}"
                        placeholder="Enter press"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 
                            text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 
                            focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Ply Section --}}
            <div class="mt-6" 
                x-data="{ 
                    ply: '{{ old('ply', $report->ply ?? 0) }}', 
                    plyType: '{{ old('ply_types', $report->ply_types ?? '') }}'
                }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ply</label>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="ply" value="1" x-model="ply"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="ply" value="0" x-model="ply"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">No</span>
                    </label>
                </div>

                <!-- Ply Options: only show if Yes is selected -->
                <div x-show="ply == '1'" class="mt-2 space-y-2">
                    <div class="flex gap-4">
                        <template x-for="type in ['clothe','metal','others']" :key="type">
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                    name="ply_types" 
                                    :value="type" 
                                    x-model="plyType"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                                <span class="ml-2" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></span>
                            </label>
                        </template>
                    </div>

                    <!-- Text field only shows if 'others' is selected -->
                    <input type="text" 
                        name="ply_type_others" 
                        placeholder="If others, specify"
                        x-show="plyType === 'others'" 
                        x-bind:required="plyType === 'others'"
                        class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        value="{{ old('ply_type_others', $report->ply_type_others ?? '') }}">
                </div>
            </div>


            {{-- Metal Insert --}}
            <div class="mt-6" 
                x-data="{ 
                    metalInsert: '{{ old('metal_insert', $report->metal_insert ?? 0) }}',
                    metalType: '{{ old('metal_insert_types', $report->metal_insert_types ?? '') }}'
                }">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metal Insert</label>
                
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="metal_insert" value="1" x-model="metalInsert"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Yes</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="metal_insert" value="0" x-model="metalInsert"
                            class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">No</span>
                    </label>
                </div>

                <!-- Metal Insert Options: only show if Yes is selected -->
                <div x-show="metalInsert === '1'" class="mt-2 space-y-2">
                    <div class="flex gap-4">
                        @foreach(['screws', 'bolt_nuts', 'plate'] as $type)
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" 
                                    name="metal_insert_types" 
                                    value="{{ $type }}"
                                    @checked(old('metal_insert_types', $report->metal_insert_types ?? '') === $type)
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                                <span class="ml-2">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Details field (always shown with options) -->
                <input type="text" name="metal_insert_details" placeholder="Details..."
                    value="{{ old('metal_insert_details', $report->metal_insert_details ?? '') }}"
                    class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>


    

            {{-- Remarks --}}
            <div class="mt-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Remarks
                </label>
                <textarea
                    id="remarks"
                    name="remarks"
                    rows="3"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 
                        text-gray-700 dark:text-gray-300
                        border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >{{ old('remarks', $editing ? $report->remarks : '') }}</textarea>
            </div>
        </div>

        @if ($editing)
        <div x-data="productionEntries({ reportId: '{{ $report->id }}' })" x-init="fetchEntries()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Production Entries</h3>
                    <div class="flex justify-start items-center mb-4 space-x-2">
                        <button type="button"
                            @click="openModal(1)"
                            :disabled="entries.some(e => e.salang_number === 1)"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Add Salang 1
                        </button>

                        <button type="button"
                            @click="openModal(2)"
                            :disabled="!entries.some(e => e.salang_number === 1) || entries.some(e => e.salang_number === 2)"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Add Salang 2
                        </button>

                        <button type="button"
                            @click="openModal(3)"
                            :disabled="!entries.some(e => e.salang_number === 2) || entries.some(e => e.salang_number === 3)"
                            class="px-4 py-2 bg-yellow-600 text-white rounded hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed">
                            Add Salang 3
                        </button>
                    </div>
            </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded shadow-md">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
            <tr>
                <x-table-column class="w-16 text-center">#</x-table-column>
                <x-table-column class="text-left">Date</x-table-column>
                <x-table-column class="text-right">Time</x-table-column>
                <x-table-column class="text-right">Temp</x-table-column>
                <x-table-column class="text-right">Begin Wt</x-table-column>
                <x-table-column class="text-right">End Wt</x-table-column>
                <x-table-column class="text-right">Qty</x-table-column>
                <x-table-column class="text-right">Actions</x-table-column>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <template x-for="(entry, idx) in entries.sort((a, b) => a.salang_number - b.salang_number)" :key="entry.id">
                <tr>
                    <td class="text-center px-4 py-3" x-text="idx + 1"></td>
                    <td class="px-4 py-3 text-left" x-text="entry.production_date"></td>
                    <td class="px-4 py-3 text-right" x-text="entry.time_per_salang"></td>
                    <td class="px-4 py-3 text-right" x-text="entry.temperature"></td>
                    <td class="px-4 py-3 text-right" x-text="entry.beginning_weight"></td>
                    <td class="px-4 py-3 text-right" x-text="entry.ending_weight"></td>
                    <td class="px-4 py-3 text-right" x-text="entry.quantity_made"></td>
                    <td class="text-right px-4 py-3 space-x-2">
                        <button type="button" @click="editEntry(entry)" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                        <button type="button" @click="deleteEntry(entry.id)" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </template>

            <tr x-show="entries.length === 0">
                <td colspan="8" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">No entries added yet</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Entry Modal -->
<x-modal name="entry-modal" maxWidth="2xl" x-show="showModal" x-cloak>
    <div class="p-6">
        <h2 class="text-lg font-semibold mb-4 dark:text-gray-200" x-text="form.id ? 'Edit Entry' : 'Add Entry'"></h2>
        <input type="hidden" x-model="form.salang_number">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Production Date -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Production Date</label>
                <input type="date" x-model="form.production_date"
                       class="w-full px-3 py-2 mt-1 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>

            <!-- Time per Salang -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Time per Salang</label>
                <input type="text" x-model="form.time_per_salang"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>

            <!-- Temperature -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Temperature</label>
                <input type="text" x-model="form.temperature"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>

            <!-- Weight per Salang -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Weight per Salang</label>
                <input type="text" x-model="form.weight_per_salang"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Finished Weight With Overflow</label>
                <input type="text" x-model="form.finished_weight_with_overflow"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Finished Weight Without Overflow</label>
                <input type="text" x-model="form.finished_weight_without_overflow"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>
                <div x-data="{ 
                    form: { 
                        is_linear: @json((bool) $report->is_linear),
                        quantity_made: @json($report->quantity_made ?? '') // preload if editing
                    } 
                }">
                    <div x-show="!form.is_linear">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Quantity Made</label>
                        <input type="text" x-model="form.quantity_made"
                            class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
                        <p class="text-sm text-red-500">
                            <span x-text="form.is_linear ? 'Linear' : 'Not Linear'"></span>
                        </p>
                    </div>
                </div>

            <!-- Beginning Weight (Salang 1 only) -->
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Beginning Weight</label>
                <input type="text" x-model="form.beginning_weight"
                       :disabled="form.salang_number !== 1"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
            </div>

            <!-- Finished Weight Group (Salang 3 only) -->
            <div x-show="form.salang_number === 3" class="space-y-3 md:col-span-2">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Ending Weight</label>
                <input type="text" x-model="form.ending_weight"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />

                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Raw Material Weight per Salang</label>
                <input type="text" x-model="form.raw_material_weight_salang"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />

                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Finished Product Weight</label>
                <input type="text" x-model="form.finished_product_weight"
                       class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />

                <div class="grid grid-cols-3 gap-3">
                    <div 
                        x-data="{ 
                            form: { 
                                is_linear: @json((bool) $report->is_linear),
                                quantity_made: @json($report->quantity_made ?? '') 
                            } 
                        }"
                    >
                        <div x-show="form.is_linear">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Quantity Made
                            </label>
                            <input type="number" 
                                x-model="form.quantity_made"
                                class="w-full px-3 py-2 border rounded 
                                    dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
                            <p class="text-sm text-red-500">
                                <span x-text="form.is_linear ? 'Linear' : 'Not Linear'"></span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Reject Quantity</label>
                        <input type="text" x-model="form.reject_quantity"
                               class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Balance</label>
                        <input type="text" x-model="form.balance"
                               class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600" />
                    </div>
                </div>
            </div>

            <!-- Remarks & Checklist -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                <textarea rows="2" x-model="form.remarks"
                          class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"></textarea>

                <div class="mt-3 space-y-2">
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="form.correct_size" class="form-checkbox" />
                            <span class="ml-2">Correct Size</span>
                        </label>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="form.correct_color" class="form-checkbox" />
                            <span class="ml-2">Correct Color</span>
                        </label>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" x-model="form.correct_hardness" class="form-checkbox" />
                            <span class="ml-2">Correct Hardness</span>
                        </label>
                    </div>
                </div>
            </div>

        </div>

        <!-- Modal Buttons -->
        <div class="flex justify-end mt-6 space-x-3">
            <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">Cancel</button>
            <button type="button" @click="saveEntry()" :disabled="isSubmitting"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
                <template x-if="!isSubmitting"><span>Save</span></template>
                <template x-if="isSubmitting"><span>Saving...</span></template>
            </button>
        </div>
    </div>
</x-modal>

</div>
@endif


<div class="flex items-center justify-between mt-10 w-full">

    {{-- LEFT: Back & Save --}}
    <div class="flex space-x-4">
        {{-- Back Button --}}
        <a href="{{ route('production-detail-reports.index') }}"
            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-chevron-left mr-1"></i> Back to List
        </a>

        {{-- Save Button --}}
        <button type="button"
                @click="confirmSave($event)"
                :disabled="isSubmitting"
                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white 
                    bg-blue-600 hover:bg-blue-700 
                    focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
            <span x-show="!isSubmitting">Save Changes</span>
            <span x-show="isSubmitting">Saving...</span>
        </button>
    </div>

    {{-- RIGHT: Submit Report --}}
    @if ($report->status !== 'completed' && $editing)
        <div 
            x-data="{
                isSubmitting: false,
                confirmSubmit() {
                    Swal.fire({
                        title: 'Submit Report?',
                        text: 'This will mark the report as completed.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Submit'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isSubmitting = true;
                            fetch('{{ route('production-detail-reports.submit', $report->id) }}', {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                }
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Submission failed');
                                return response.json();
                            })
                            .then(data => {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Report submitted successfully!',
                                    showConfirmButton: false,
                                    timer: 2500,
                                    timerProgressBar: true
                                }).then(() => {
                                    window.location.href = '{{ route('production-detail-reports.index') }}';
                                });
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Something went wrong',
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                }
            }"
        >
            <button type="button"
                @click="confirmSubmit"
                :disabled="isSubmitting"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm shadow-sm">
                <span x-show="!isSubmitting">
                    <i class="fas fa-check-circle mr-1"></i> Submit Report
                </span>
                <span x-show="isSubmitting">
                    <i class="fas fa-spinner fa-spin mr-1"></i> Submitting...
                </span>
            </button>
        </div>
    @endif
</div>

</form>
<script> 
function productionEntries({ reportId }) {
    return {
        reportId,
        showModal: false,
        isSubmitting: false,
        entries: [],
        form: {
            id: null,
            salang_number: null,
            production_date: '',
            time_per_salang: '',
            weight_per_salang: '',
            finished_weight_with_overflow: '',
            finished_weight_without_overflow: '',
            temperature: '',
            beginning_weight: '',
            ending_weight: '',
            raw_material_weight_salang: '',
            quantity_made: '',
            reject_quantity: '',
            balance: '',
            finished_product_weight: '',
            remarks: '',
            correct_size: false,
            correct_color: false,
            correct_hardness: false
        },

        init() {
            console.log('Report ID:', this.reportId);
            this.fetchEntries();
        },

        // Open modal
        openModal(salangNumber = null) {
            this.resetForm();
            if (salangNumber) this.form.salang_number = salangNumber;

            // Set defaults for Salang 3 fields
            if (salangNumber === 3) {
                this.form.ending_weight = '0';
                this.form.raw_material_weight_salang = '0';
                this.form.finished_product_weight = '0';
                this.form.reject_quantity = '0';
                this.form.balance = '0';
            }

            this.showModal = true;
            this.$dispatch('open-modal', 'entry-modal');
        },

        // Close modal
        closeModal() {
            this.showModal = false;
            this.$dispatch('close-modal', 'entry-modal');
        },

        // Edit entry
        editEntry(entry) {
            this.resetForm();
            this.form = { ...entry };

            this.form.correct_size = !!entry.correct_size;
            this.form.correct_color = !!entry.correct_color;
            this.form.correct_hardness = !!entry.correct_hardness;

            // Ensure Salang 3 defaults if empty
            if (this.form.salang_number === 3) {
                this.form.ending_weight ||= '0';
                this.form.raw_material_weight_salang ||= '0';
                this.form.finished_product_weight ||= '0';
                this.form.quantity_made ||= '0';
                this.form.reject_quantity ||= '0';
                this.form.balance ||= '0';
            }

            this.showModal = true;
            this.$dispatch('open-modal', 'entry-modal');
        },

        // Reset form
        resetForm() {
            this.form = {
                id: null,
                salang_number: null,
                production_date: '',
                time_per_salang: '',
                weight_per_salang: '',
                finished_weight_with_overflow: '',
                finished_weight_without_overflow: '',
                temperature: '',
                beginning_weight: this.entries.find(e => e.salang_number === 1)?.beginning_weight || '',
                ending_weight: '',
                raw_material_weight_salang: '',
                quantity_made: '',
                reject_quantity: '',
                balance: '',
                finished_product_weight: '',
                remarks: '',
                correct_size: false,
                correct_color: false,
                correct_hardness: false
            };
        },

        // Fetch entries
        async fetchEntries() {
            try {
                const response = await fetch(`/production-detail-reports/${this.reportId}/entries`);
                this.entries = await response.json();
            } catch (err) {
                console.error('Fetch error:', err);
            }
        },

        // Save or update entry
        async saveEntry() {
        if (!this.form.salang_number) {
            Swal.fire({
                icon: 'error',
                title: 'Salang number missing',
                text: 'Please specify Salang 1, 2, or 3'
            });
            return;
        }

        const duplicate = this.entries.find(e => e.salang_number === this.form.salang_number && e.id !== this.form.id);
        if (!this.form.id && duplicate) {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Entry',
                text: `Salang ${this.form.salang_number} already exists`
            });
            return;
        }

        this.isSubmitting = true;

        try {
            const isEdit = !!this.form.id;
            const url = isEdit
                ? `/production-detail-entries/${this.form.id}`
                : `/production-detail-reports/${this.reportId}/entries`;
            const method = isEdit ? 'PUT' : 'POST';

            // Ensure all numeric/text fields are strings
            const sanitizeString = value => (value === null || value === undefined ? '' : String(value));

            const payload = {
                ...this.form,
                beginning_weight: sanitizeString(this.form.beginning_weight || this.entries.find(e => e.salang_number === 1)?.beginning_weight),
                ending_weight: sanitizeString(this.form.ending_weight || (this.form.salang_number === 3 ? '0' : '')),
                raw_material_weight_salang: sanitizeString(this.form.raw_material_weight_salang || (this.form.salang_number === 3 ? '0' : '')),
                finished_product_weight: sanitizeString(
                    this.form.finished_product_weight !== '' ? this.form.finished_product_weight : (this.form.salang_number === 3 ? '0' : '')
                ),
                quantity_made: sanitizeString(this.form.quantity_made || '0'),
                reject_quantity: sanitizeString(this.form.reject_quantity || (this.form.salang_number === 3 ? '0' : '')),
                balance: sanitizeString(this.form.balance || (this.form.salang_number === 3 ? '0' : '')),
                correct_size: !!this.form.correct_size,
                correct_color: !!this.form.correct_color,
                correct_hardness: !!this.form.correct_hardness,
                remarks: sanitizeString(this.form.remarks)
            };

            console.log('Payload being sent:', payload);

            const response = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (response.ok) {
                this.closeModal();
                await this.fetchEntries();
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: isEdit ? 'Entry updated' : 'Entry created',
                    showConfirmButton: false,
                    timer: 2500
                });
            } else if (response.status === 422 && data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('<br>');
                Swal.fire({ icon: 'error', title: 'Validation Failed', html: errorMessages });
            } else {
                Swal.fire({ icon: 'error', title: 'Save Failed', html: data.message || 'Something went wrong.' });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: 'Unexpected Error', text: err.message });
        } finally {
            this.isSubmitting = false;
        }
    },

        // Delete entry
        async deleteEntry(id) {
            const result = await Swal.fire({
                title: 'Are you sure?',
                text: 'This entry will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            });
            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/production-detail-entries/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });

                if (response.ok) {
                    await this.fetchEntries();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Entry deleted', showConfirmButton: false, timer: 2500 });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Unexpected Error', text: err.message });
            }
        },

        // Check button availability
        canAddSalang(number) {
            if (number === 1) return !this.entries.some(e => e.salang_number === 1);
            if (number === 2) return this.entries.some(e => e.salang_number === 1) && !this.entries.some(e => e.salang_number === 2);
            if (number === 3) return this.entries.some(e => e.salang_number === 2) && !this.entries.some(e => e.salang_number === 3);
            return false;
        }
    };
}

function jobOrderSelect(isEditMode) {
    return {
        isEditMode: isEditMode,

        // Search query and results
        query: isEditMode ? '' : @json(old('jo_no', $report->jo_no ?? '')),
        results: [],
        showResults: false,

        // Selected Job Order
        selected: {
            id: @json(old('job_order_id', $report->job_order_id ?? null)),
            jo_no: @json(old('jo_no', $report->jo_no ?? '')),
            customer_name: @json(old('customer_name', optional($report->jobOrder?->salesOrder?->customer)->name ?? '--')),
            so_no: @json(old('so_no', optional($report->jobOrder?->salesOrder)->so_no ?? '--')),
            po_no: @json(old('po_no', optional($report->jobOrder?->salesOrder)->po_no ?? '--')),
            pr_no: @json(old('pr_no', optional($report->jobOrder?->salesOrder)->pr_no ?? '--')),
            sales_rep: @json(old('sales_rep', optional($report->jobOrder?->salesOrder?->salesRep?->employee)->full_name ?? '--')),
            delivery_details: @json(old('delivery_details', $report->jobOrder?->delivery_details ?? '--')),
            remarks: @json(old('remarks', $report->jobOrder?->remarks ?? '--'))
        },

        // Job Order Items
        joItems: @json(old('joItems', $joItems ?? [])),
        selectedJoItem: @json(old('jo_item_id', $report->jo_item_id ?? null)),

        // Flags
        ply: {{ old('ply', $report->ply ?? false) ? 'true' : 'false' }},
        metalInsert: {{ old('metal_insert', $report->metal_insert ?? false) ? 'true' : 'false' }},

        // Selected JO item details
        isLinear: {{ old('is_linear', $report->jobOrderItem?->is_linear ?? false) ? 'true' : 'false' }},
        productionType: '{{ old('production_type', $report->production_type ?? '') }}',

        // Computed property for the selected JO item object
        get selectedJoItemObj() {
            // 👀 make sure we return a usable object
            return this.joItems.find(item => item.id == this.selectedJoItem) ?? null;
        },

        // Init method with preloading of the selected JO item
        init() {
            // If editing and no joItems preloaded, fetch them
            if (this.isEditMode && this.selected.id && (!this.joItems || this.joItems.length === 0)) {
                fetch(`/api/job-orders/${this.selected.id}/items`)
                    .then(res => res.json())
                    .then(items => {
                        this.joItems = items;
                    });
            }

            // If a JO item is already selected, fetch details
            if (this.selectedJoItem) {
                this.fetchItemDetails(this.selectedJoItem);
            }
        },

        // Search Job Orders
        searchJO() {
            if (this.isEditMode || this.query.length < 2) {
                this.results = [];
                this.showResults = false;
                return;
            }

            fetch(`/api/job-orders/search?q=${encodeURIComponent(this.query)}`)
                .then(res => res.json())
                .then(data => {
                    this.results = data;
                    this.showResults = true;
                });
        },

        // Pick a Job Order
        pickJO(jo) {
            if (this.isEditMode) return;

            this.selected = jo;
            this.query = jo.jo_no;
            this.results = [];
            this.showResults = false;

            fetch(`/api/job-orders/${jo.id}/items`)
                .then(res => res.json())
                .then(items => {
                    this.joItems = items;
                    this.selectedJoItem = items.length ? items[0].id : null;

                    if (this.selectedJoItem) {
                        this.fetchItemDetails(this.selectedJoItem);
                    }
                });
        },

        // Fetch JO item-specific details
        fetchItemDetails(id) {
            if (!id) return;

            fetch(`/job-orders/items/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    this.isLinear = data.is_linear ?? false;
                    this.productionType = data.production_type ?? '';

                    // Update hidden inputs
                    const isLinearInput = document.querySelector('input[name="is_linear"]');
                    if (isLinearInput) isLinearInput.value = this.isLinear ? '1' : '0';

                    const productionTypeInput = document.querySelector('input[name="production_type"]');
                    if (productionTypeInput) productionTypeInput.value = this.productionType;
                })
                .catch(err => {
                    this.isLinear = false;
                    this.productionType = '';
                    console.error("Failed to fetch JO item details", err);
                });
        }
    };
}


</script>