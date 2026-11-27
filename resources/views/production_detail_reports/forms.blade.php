@props(['report'])

@php
    $editing = isset($report) && $report->exists;
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
        @method('PUT')
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Item from Job Order <span class="text-red-500">*</span>
                    </label>

                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-2 py-4 text-center">Select</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Item Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Item Description
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-for="item in joItems" :key="item.id">
                                <tr class="text-sm text-gray-800 dark:text-gray-100">
                                    <td class="px-2 py-4 text-center">
                                        <input type="radio"
                                            name="selected_item"
                                            :value="item.id"
                                            x-model="selectedJoItem"
                                            @change="fetchItemDetails(item.id)"
                                            class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </td>
                                    <td class="px-4 py-2" x-text="item.item_name"></td>
                                    <td class="px-4 py-2" x-text="item.item_description"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                            <div class="col-span-3 mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Production Type
                                </label>
                            </div>

                            <template x-for="type in ['old','new','trade']" :key="type">
                                <label class="inline-flex items-center mr-4">
                                    <input type="radio"
                                        name="production_type"
                                        :value="type"
                                        x-model="productionType">
                                    <span class="ml-2" x-text="type.charAt(0).toUpperCase() + type.slice(1)"></span>
                                </label>
                            </template>

                            <!-- Linear / Non-linear Radios -->
                            <div class="col-span-3 mt-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Linear</label>
                            </div>

                            <label class="inline-flex items-center mr-4">
                                <input type="radio"
                                    name="is_linear"
                                    :value="true"
                                    x-model="isLinear">
                                <span class="ml-2">Yes</span>
                            </label>

                            <label class="inline-flex items-center">
                                <input type="radio"
                                    name="is_linear"
                                    :value="false"
                                    x-model="isLinear">
                                <span class="ml-2">No</span>
                            </label>
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
        ply: @js(old('ply', $report->ply ?? '0')), 
        plyType: @js(old('ply_types', $report->ply_types ?? '')),
        resetPly() { 
            if (this.ply == '0') { 
                this.plyType = ''; 
            }
        }
    }"
    x-init="resetPly()"
    x-watch="ply" @watch="resetPly()">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ply</label>
    <div class="flex items-center space-x-6">
        <label class="inline-flex items-center">
            <input type="radio" name="ply" value="1" x-model="ply" class="text-blue-600 focus:ring-blue-500">
            <span class="ml-2">Yes</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="ply" value="0" x-model="ply" class="text-blue-600 focus:ring-blue-500">
            <span class="ml-2">No</span>
        </label>
    </div>

    <!-- Show subchoices if plyType already has value OR ply == '1' -->
    <div x-show="plyType !== '' || ply == '1'" x-cloak class="mt-2 space-y-2">
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
        metalInsert: @js(old('metal_insert', $report->metal_insert ?? '0')),
        metalType: @js(old('metal_insert_types', $report->metal_insert_types ?? '')),
        resetMetal() { 
            if (this.metalInsert == '0') { 
                this.metalType = ''; 
                $refs.metalDetails.value = ''; 
            }
        }
    }"
    x-init="resetMetal()"
    x-watch="metalInsert" @watch="resetMetal()">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metal Insert</label>
    
    <div class="flex items-center space-x-6">
        <label class="inline-flex items-center">
            <input type="radio" name="metal_insert" value="1" x-model="metalInsert" class="text-blue-600 focus:ring-blue-500">
            <span class="ml-2">Yes</span>
        </label>
        <label class="inline-flex items-center">
            <input type="radio" name="metal_insert" value="0" x-model="metalInsert" class="text-blue-600 focus:ring-blue-500">
            <span class="ml-2">No</span>
        </label>
    </div>

    <!-- Show subchoices if metalType already has value OR metalInsert == '1' -->
    <div x-show="metalType !== '' || metalInsert == '1'" x-cloak class="mt-2 space-y-2">
        <div class="flex gap-4">
            @foreach(['screws', 'bolt_nuts', 'plate'] as $type)
                <label class="inline-flex items-center cursor-pointer">
                    <input type="radio" 
                        name="metal_insert_types" 
                        value="{{ $type }}"
                        x-model="metalType"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                    <span class="ml-2">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                </label>
            @endforeach
        </div>

        <!-- Details field (resets when selecting No) -->
        <input type="text" 
            x-ref="metalDetails"
            name="metal_insert_details" 
            placeholder="Details..."
            value="{{ old('metal_insert_details', $report->metal_insert_details ?? '') }}"
            class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
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
            <div x-data="productionEntries({reportId: '{{ $report->id }}', initialPressmanName: '{{ $pressmanName }}'})">
            <!-- Hidden JSON for units -->
            <script type="application/json" id="units-json">
                {!! $units->toJson() !!}
            </script>

            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Production Entries</h3>
                @if ($editing && $report->pdr_status !== 'completed')
                    <button type="button"
                            @click="openModal()"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Create PDR
                    </button>
                @endif
            </div>


            <!-- Table -->
            <div class="overflow-x-auto rounded shadow-md">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <tr>
                            <x-table-column class="w-16 text-center">#</x-table-column>
                            <x-table-column class="text-left">Date</x-table-column>
                            <x-table-column class="text-right">Start Time</x-table-column>
                            <x-table-column class="text-right">Temp</x-table-column>
                            <x-table-column class="text-right">Begin Wt</x-table-column>
                            <x-table-column class="text-right">End Wt</x-table-column>
                            <x-table-column class="text-right">Daily Output</x-table-column>
                            <x-table-column class="text-right">Rejects</x-table-column>
                            <x-table-column class="text-right">Actions</x-table-column>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="(entry, idx) in entries" :key="entry.id">
                            <tr>
                                <td class="text-center px-4 py-3" x-text="idx + 1"></td>
                                <td class="px-4 py-3 text-left" x-text="entry.production_date"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.start_time"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.temp_setting"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.beginning_weight_rawmat"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.ending_weight_rawmat"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.daily_output"></td>
                                <td class="px-4 py-3 text-right" x-text="entry.reject_quantity"></td>
                                <td class="text-right px-4 py-3 space-x-2">
                                    <button type="button" @click="editEntry(entry)" class="text-blue-500 hover:text-blue-700">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" @click="deleteEntry(entry.id)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="entries.length === 0">
                            <td colspan="9" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                                No entries added yet
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            <!-- Entry Modal -->
<x-modal name="entry-modal" maxWidth="6xl" x-show="showModal" x-cloak>
    <div class="p-6 max-h-[80vh] overflow-y-auto">
        <!-- Title -->
        <h2 class="text-xl font-bold mb-6 text-center text-gray-800 dark:text-gray-200">
            PRODUCT DETAIL REPORT
        </h2>

        <!-- Form Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Row 1 -->
            <div class="col-span-2">
                <label class="block text-base font-semibold text-gray-800 dark:text-gray-200 mb-2">
                    PDR For
                </label>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Item Description:</label>
                <input type="text" :value="selectedJoItemObj?.item_description" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">JO No.:</label>
                <input type="text" :value="selected.jo_no" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 2 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Company Name:</label>
                <input type="text" :value="selected.customer_name" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Date:</label>
                <input type="date" x-model="form.production_date"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 3 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Temp Setting:</label>
                <input type="text" x-model="form.temp_setting"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Qty. to be Produced:</label>
                <input type="number" x-model="form.qty_to_be_produced"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 4 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Starting Time:</label>
                <input type="time" x-model="form.start_time"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Unit:</label>
                <select x-ref="unitSelect"
                        x-init="units = JSON.parse(document.getElementById('units-json').textContent)"
                        x-model.number="form.unit_id"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100">
                    <option value="" disabled>-- Select Unit --</option>
                    <template x-for="unit in units" :key="unit.id">
                        <option :value="unit.id" x-text="unit.short_name + ' (' + unit.name + ')'"></option>
                    </template>
                </select>

                <!-- Hidden input to actually post the selected unit_id -->
                <input type="hidden" name="unit_id" :value="form.unit_id">
            </div>

            <!-- Row 5 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Process Duration:</label>
                <input type="text" x-model="form.process_duration"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Material:</label>
                <input type="text" :value="selectedJoItemObj?.material" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 6 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Press No.:</label>
                <input type="text" x-model="form.press_no"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Hardness:</label>
                <input type="text" :value="selectedJoItemObj?.hardness" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 7 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Pressman:</label>
                <input type="text" :value="pressmanName" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Thickness:</label>
                    <input 
                        type="text" 
                        :value="selectedJoItemObj?.thickness + (selectedJoItemObj?.thickness_unit ? ' ' + selectedJoItemObj.thickness_unit : '')" 
                        readonly
                        class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" 
                    />
                </div>

            <!-- Row 8 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Mould Details:</label>
                <input type="text" :value="selectedJoItemObj?.mould_details" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Insert Details:</label>
                <input type="text" x-model="form.insert_details"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 9 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Beginning Weight of Rawmat:</label>
                <input type="text" x-model="form.beginning_weight_rawmat"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Ending Weight of Rawmat:</label>
                <input type="text" x-model="form.ending_weight_rawmat"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 10 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Rawmat Size/Salang:</label>
                <input type="text" x-model="form.rawmat_size_salang"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Rawmat Weight/Salang:</label>
                <input type="text" x-model="form.rawmat_weight_salang"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 11 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Overflow Weight/Salang:</label>
                <input type="text" x-model="form.overflow_weight_salang"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Actual Weight of Finish Product:</label>
                <input type="text" x-model="form.actual_finish_product_weight"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 12 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Daily Output:</label>
                <input type="number" x-model="form.daily_output"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Reject Qty:</label>
                <input type="number" x-model="form.reject_quantity"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>

            <!-- Row 13 -->
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Remaining Balance:</label>
                <input type="number" :value="form.remaining_balance" readonly
                    class="w-full px-3 py-2 border rounded bg-gray-100 dark:bg-gray-700 dark:text-gray-100" />
            </div>
            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Day No.:</label>
                <input type="number" x-model="form.day_no"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100" />
            </div>
        </div>

        <!-- Remarks -->
        <div class="mt-5">
            <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Remarks:</label>
            <textarea rows="3" x-model="form.remarks"
                    class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100"></textarea>
        </div>

        <!-- Modal Buttons -->
        <div class="flex justify-end mt-6 space-x-3">
            <button type="button" @click="closeModal()"
                    class="px-5 py-2 bg-gray-200 dark:bg-gray-600 rounded">Cancel</button>
            <button type="button" @click="saveEntry()" :disabled="isSubmitting"
                    class="px-5 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center justify-center space-x-2">
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
        @if ($editing && $report->pdr_status !== 'completed')
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
        @endif
    </div>

    <div class="flex space-x-2">
        <!-- Generate PDR Link (always visible) -->
        @if ($editing)
            <a href="{{ route('reports.pdr', $report->id) }}" target="_blank"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm shadow-sm inline-flex items-center">
                <i class="fas fa-file-alt mr-1"></i> Generate PDR
            </a>
        @endif

        {{-- Submit Report button (only show if not completed + editing + balance is 0) --}}
        @if ($report->pdr_status !== 'completed' && $editing && $report->entries->isNotEmpty() && $report->entries->last()->remaining_balance === 0)
            <button type="button"
                onclick="confirmSubmit()"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm shadow-sm">
                <i class="fas fa-check-circle mr-1"></i> Submit Report
            </button>
        @endif
    </div>
</div>

</form>
<script>
function confirmSubmit() {
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
            fetch('/production-detail-reports/{{ $report->id }}/submit', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                }
            })
            .then(res => res.json())
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
                    window.location.href = '/production-detail-reports';
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: err.message || 'Something went wrong',
                    confirmButtonText: 'OK'
                });
            });
        }
    });
} 

function productionEntries({ reportId, initialPressmanName }) {
    return {
        reportId,
        pressmanId: window.initialPressmanId || null,
        pressmanName: initialPressmanName || '',
        selected: {},
        selectedJoItemObj: {},
        showModal: false,
        isSubmitting: false,
        entries: [],
        originalQty: null, // fixed qty set by first entry
        form: {},

        init() {
            this.resetForm();
            this.fetchJoItem();
            this.fetchEntries();
        },

        // Fetch JO item
        async fetchJoItem() {
            try {
                const response = await fetch(`/production-detail-reports/${this.reportId}/item`);
                if (!response.ok) throw new Error('Failed to fetch JO item');
                this.selectedJoItemObj = await response.json();
                this.selected = {
                    jo_no: this.selectedJoItemObj.jo_no || '',
                    customer_name: this.selectedJoItemObj.customer_name || '',
                    jo_item_obj: this.selectedJoItemObj
                };
            } catch (err) {
                console.error('fetchJoItem error:', err);
            }
        },

        // Fetch entries
        async fetchEntries() {
            try {
                const response = await fetch(`/production-detail-reports/${this.reportId}/entries`);
                if (!response.ok) throw new Error('Failed to fetch entries');
                this.entries = (await response.json()).sort((a, b) => a.day_no - b.day_no);

                if (this.entries.length > 0) {
                    this.originalQty = this.entries[0].qty_to_be_produced;
                    this.form.unit_id = this.entries[0].unit_id; // ✅ retain the same unit
                } else {
                    this.originalQty = null;
                }

                console.log('Original qty_to_be_produced:', this.originalQty);
            } catch (err) {
                console.error('fetchEntries error:', err);
            }
        },

        // Open modal (new or edit)
        openModal(entry = null) {
            this.resetForm();

            if (entry) {
                this.form = { ...this.form, ...entry };
            } else {
                this.form.qty_to_be_produced = this.originalQty; 
                const lastEntry = [...this.entries].reverse().find(e => e.remaining_balance != null);
                this.form.remaining_balance = lastEntry
                    ? lastEntry.remaining_balance
                    : this.form.qty_to_be_produced;
            }

            this.showModal = true;
            this.$dispatch('open-modal', 'entry-modal');
        },

        editEntry(entry) {
            this.openModal(entry);
        },

        closeModal() {
            this.showModal = false;
            this.$dispatch('close-modal', 'entry-modal');
        },

        deleteEntry(entryId) {
            if (!confirm('Are you sure you want to delete this entry?')) return;
            fetch(`/production-detail-entries/${entryId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(async response => {
                if (response.ok) {
                    await this.fetchEntries();
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Entry deleted', showConfirmButton: false, timer: 2500 });
                }
            })
            .catch(err => {
                console.error('deleteEntry error:', err);
                Swal.fire({ icon: 'error', title: 'Unexpected Error', text: err.message });
            });
        },

        resetForm() {
        this.form = {
            id: null,
            production_detail_report_id: this.reportId,
            pressman_id: this.pressmanId,
            production_date: '',
            daily_output: '',
            reject_quantity: '',
            day_no: '',
            press_no: '',
            temp_setting: '',
            start_time: '',
            process_duration: '',
            insert_details: '',
            beginning_weight_rawmat: '',
            ending_weight_rawmat: '',
            rawmat_size_salang: '',
            rawmat_weight_salang: '',
            overflow_weight_salang: '',
            actual_finish_product_weight: '',
            remarks: '',
            qty_to_be_produced: this.originalQty || '',
            remaining_balance: '',
            unit_id: this.form?.unit_id || '',
        };
    },


        onDailyOutputChange() {
            const dailyOutput = Number(this.form.daily_output || 0);
            const baseQty = Number(this.originalQty || this.form.qty_to_be_produced || 0);

            let previousOutput = 0;
            if (this.form.id) {
                for (const e of this.entries) {
                    if (e.id === this.form.id) break;
                    previousOutput += Number(e.daily_output || 0);
                }
            } else {
                previousOutput = this.entries.reduce((sum, e) => sum + Number(e.daily_output || 0), 0);
            }

            this.form.remaining_balance = Math.max(baseQty - (previousOutput + dailyOutput), 0);
        },

        async saveEntry() {
            this.isSubmitting = true;
            try {
                const isEdit = !!this.form.id;
                const url = isEdit
                    ? `/production-detail-entries/${this.form.id}`
                    : `/production-detail-reports/${this.reportId}/entries`;
                const method = isEdit ? 'PUT' : 'POST';

                // Payload: always include qty on first save, ignore afterwards
                const payload = {
                    production_date: this.form.production_date,
                    daily_output: this.form.daily_output,
                    reject_quantity: this.form.reject_quantity,
                    day_no: this.form.day_no,
                    press_no: this.form.press_no,
                    temp_setting: this.form.temp_setting,
                    start_time: this.form.start_time,
                    process_duration: this.form.process_duration,
                    insert_details: this.form.insert_details,
                    beginning_weight_rawmat: this.form.beginning_weight_rawmat,
                    ending_weight_rawmat: this.form.ending_weight_rawmat,
                    rawmat_size_salang: this.form.rawmat_size_salang,
                    rawmat_weight_salang: this.form.rawmat_weight_salang,
                    overflow_weight_salang: this.form.overflow_weight_salang,
                    actual_finish_product_weight: this.form.actual_finish_product_weight,
                    remarks: this.form.remarks,
                    unit_id: this.form.unit_id,
                    ...(this.entries.length === 0 ? { qty_to_be_produced: this.form.qty_to_be_produced } : {}),
                };

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    if (response.status === 422 && data.errors) {
                        Swal.fire({ icon: 'error', title: 'Validation Failed', html: Object.values(data.errors).flat().join('<br>') });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Save Failed', html: data.message || 'Something went wrong.' });
                    }
                    return;
                }

                await this.fetchEntries();
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Entry saved successfully!', showConfirmButton: false, timer: 2500 });

                this.closeModal();
            } catch (err) {
                console.error('saveEntry error:', err);
                Swal.fire({ icon: 'error', title: 'Unexpected Error', text: err.message });
            } finally {
                this.isSubmitting = false;
            }
        }
    };
}


function jobOrderSelect(isEditMode) {
    return {
        isEditMode: isEditMode,

        query: isEditMode ? '' : @json(old('jo_no', $report->jo_no ?? '')),
        results: [],
        showResults: false,

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

        joItems: @json(old('joItems', $joItems ?? [])),
        selectedJoItem: @json(old('jo_item_id', $report->jo_item_id ?? null)),

        ply: {{ old('ply', $report->ply ?? false) ? 'true' : 'false' }},
        metalInsert: {{ old('metal_insert', $report->metal_insert ?? false) ? 'true' : 'false' }},

        isLinear: {{ old('is_linear', $report->jobOrderItem?->is_linear ?? false) ? 'true' : 'false' }},
        productionType: '{{ old('production_type', $report->production_type ?? '') }}',

        get selectedJoItemObj() {
            return this.joItems.find(item => item.id == this.selectedJoItem) ?? null;
        },

        init() {
            if (this.isEditMode && this.selected.id && (!this.joItems || this.joItems.length === 0)) {
                this.fetchItems(this.selected.id);
            }

            if (this.selectedJoItem) {
                this.fetchItemDetails(this.selectedJoItem);
            }
        },

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

        pickJO(jo) {
            if (this.isEditMode) return;

            this.selected = jo;
            this.query = jo.jo_no;
            this.results = [];
            this.showResults = false;

            this.fetchItems(jo.id);
        },

        fetchItems(jobOrderId) {
            fetch(`/api/job-orders/${jobOrderId}/items`)
                .then(res => res.json())
                .then(items => {
                    // Map API response to include item_desc consistently
                    this.joItems = items.map(i => ({
                        id: i.id,
                        item_name: i.item_name,
                        item_description: i.item_desc, // <- use item_desc from API
                        is_linear: !!i.is_linear
                    }));

                    // Select the first item by default
                    this.selectedJoItem = this.joItems.length ? this.joItems[0].id : null;

                    if (this.selectedJoItem) {
                        this.fetchItemDetails(this.selectedJoItem);
                    }
                });
        },

        fetchItemDetails(id) {
            if (!id) return;

            fetch(`/job-orders/items/${id}/details`)
                .then(res => res.json())
                .then(data => {
                    this.isLinear = !!data.is_linear;
                    this.productionType = data.production_type ?? '';
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