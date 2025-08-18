@props(['report'])

@php
    $editing = isset($report) && $report->exists;
    $salang1 = $editing ? $report->salang_1 ?? [] : [];
    $salang2 = $editing ? $report->salang_2 ?? [] : [];
    $salang3 = $editing ? $report->salang_3 ?? [] : [];
    $salangs = [$salang1, $salang2, $salang3];

    $plyTypes = $editing ? ($report->ply_types ?? []) : [];
    $metalInsertTypes = $editing ? ($report->metal_insert_types ?? []) : [];

    $joItems = $editing ? $report->jobOrder?->jo_items ?? [] : [];
@endphp

<form
    method="POST"
    action="{{ $editing ? route('production-detail-reports.update', parameters: $report) : route('production-detail-reports.store') }}"
    class="space-y-6"
    x-data="{
        readyToUse: {{ $editing && $report->is_ready_to_use ? 'true' : 'false' }},
        ply: {{ $editing && $report->ply ? 'true' : 'false' }},
        metalInsert: {{ $editing && $report->metal_insert ? 'true' : 'false' }},
    }"
>
    @csrf
    @if($editing) @method('PUT') @endif

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
            <input type="text" name="job_order_id" :value="selected?.id">
            <input type="text" name="jo_no" :value="selected?.jo_no">
            {{-- <input type="text" name="jo_item_id" :value="selectedJoItem"> --}}

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
                        <div class="col-span-2 mt-6 border-t border-gray-600 pt-4" x-show="isEditMode && selectedJoItemObj">
                            <p class="text-sm font-medium text-gray-400">Selected Item</p>
                            <p class="text-basetext-gray-900 dark:text-gray-100" x-text="(selectedJoItemObj?.item_name || '--') + ' — ' + (selectedJoItemObj?.item_desc || '')"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            <!-- First Row: Date, Press, Temperature -->
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
                    <label for="press"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Press
                    </label>
                    <input
                        type="text"
                        name="press"
                        value="{{ old('press', $report->press) }}"
                        placeholder="Enter press"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Temperature --}}
                <div>
                    <label for="temperature"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Temperature
                    </label>
                    <input
                        type="text"
                        name="temperature"
                        value="{{ old('temperature', $report->temperature) }}"
                        placeholder="Enter temperature"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Second Row: Old, New, Ready to Use -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                {{-- Old Checkbox --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Production Type</label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_old_checked" @checked(old('is_old_checked', $report->is_old_checked))
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Old</span>
                    </label>
                </div>

                {{-- New Checkbox --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_new_checked" @checked(old('is_new_checked', $report->is_new_checked))
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">New</span>
                    </label>
                </div>

                {{-- Ready to Use --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_ready_to_use" x-model="readyToUse"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Ready to Use</span>
                    </label>
                </div>
            </div>

            {{-- Ply Section --}}
            <div class="mt-6">
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

                <div x-show="ply" class="mt-2 space-y-2">
                    <div class="flex gap-4">
                        @foreach(['clothe', 'metal', 'others'] as $type)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="ply_types[]"
                                    value="{{ $type }}"
                                    @checked(in_array($type, old('ply_types', $plyTypes)))
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                                <span class="ml-2">{{ ucfirst($type) }}</span>
                            </label>
                        @endforeach
                    </div>
                   <input type="text" name="ply_type_others" placeholder="If others, specify"
                        value="{{ old('ply_type_others', $report->ply_type_others ?? '') }}"
                        class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">

                </div>
            </div>

            {{-- Metal Insert --}}
            <div class="mt-6">
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

                <div x-show="metalInsert" class="mt-2 space-y-2">
                    <div class="flex gap-4">
                        @foreach(['screws', 'bolt_nuts', 'plate'] as $type)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="metal_insert_types[]"
                                    value="{{ $type }}"
                                    @checked(in_array($type, old('metal_insert_types', $metalInsertTypes)))
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-offset-0 focus:ring-blue-200 focus:ring-opacity-50 dark:border-gray-600 dark:bg-gray-700">
                                <span class="ml-2">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                            </label>
                        @endforeach
                    </div>
                    <input type="text" name="metal_insert_details" placeholder="Details..."
                        value="{{ old('metal_insert_details', $report->metal_insert_details) }}"
                        class="w-full px-4 py-2 mt-1 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            {{-- Remarks --}}
            <div class="mt-6">
                <label for="remarks"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                <textarea name="remarks" rows="4"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('remarks', $report->remarks) }}</textarea>
            </div>
        </div>
    </div>


    {{-- Salang Section --}}
    <div x-show="readyToUse" class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Ready to Use - Salangs</h3>

        @for($i = 1; $i <= 3; $i++)
            @php $salang = $salangs[$i - 1]; @endphp
            <div class="border-t pt-4 mt-4">
                <h4 class="font-medium text-gray-600 dark:text-gray-300 mb-2">Salang {{ $i }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        'raw_material_weight' => 'Weight of raw materials',
                        'temperature' => 'Temperature',
                        'press' => 'Press',
                        'weight_per_salang' => 'Weight per salang',
                        'finished_with_overflow' => 'Finished product weight (with overflow)',
                        'finished_without_overflow' => 'Finished product weight (without overflow)',
                    ] as $field => $label)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $label }}</label>
                            <input name="salang_{{ $i }}[{{ $field }}]" type="text"
                                value="{{ old("salang_{$i}.{$field}", $salang[$field] ?? '') }}"
                                class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                    border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    @endforeach

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                        <textarea name="salang_{{ $i }}[remarks]" rows="2"
                            class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old("salang_{$i}.remarks", $salang['remarks'] ?? '') }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Checklist</label>
                        <div class="space-y-4">
                            @foreach(['correct_size' => 'Correct Size', 'correct_color' => 'Correct Color', 'correct_hardness' => 'Correct Hardness'] as $key => $label)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    <div class="flex space-x-6 text-sm text-gray-700 dark:text-gray-300">
                                        @foreach(['yes' => 'Yes', 'no' => 'No'] as $val => $text)
                                            <label class="inline-flex items-center">
                                                <input type="radio"
                                                    name="salang_{{ $i }}[checklist][{{ $key }}]"
                                                    value="{{ $val }}"
                                                    @checked(old("salang_{$i}.checklist.$key", $salang['checklist'][$key] ?? '') == $val)
                                                    class="text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                                                <span class="ml-2">{{ $text }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endfor

    </div>


<div x-data="productionEntries({ reportId: {{ $report->id }} })" x-init="fetchEntries()">

    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Production Entries</h3>
        <button type="button" @click="openModal()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-1"></i> Add Entry
        </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded shadow-md">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                <tr>
                    <x-table-column class="w-16 text-center">#</x-table-column>
                    <x-table-column>Date</x-table-column>
                    <x-table-column>Time</x-table-column>
                    <x-table-column>Temp</x-table-column>
                    <x-table-column>Begin Wt</x-table-column>
                    <x-table-column>End Wt</x-table-column>
                    <x-table-column>Qty</x-table-column>
                    <x-table-column class="text-right">Actions</x-table-column>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <template x-for="(entry, idx) in entries" :key="entry.id">
                    <tr>
                        <td class="text-center px-4 py-3" x-text="idx + 1"></td>
                        <td class="px-4 py-3" x-text="entry.production_date"></td>
                        <td class="px-4 py-3" x-text="entry.production_time"></td>
                        <td class="px-4 py-3" x-text="entry.temperature"></td>
                        <td class="px-4 py-3" x-text="entry.beginning_weight"></td>
                        <td class="px-4 py-3" x-text="entry.ending_weight"></td>
                        <td class="px-4 py-3" x-text="entry.quantity_made"></td>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <template x-for="[field, label] in Object.entries(fieldLabels)" :key="field">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300" x-text="label"></label>
                        <input :type="field.includes('date') ? 'date' : field.includes('time') ? 'time' : 'number'"
                               step="0.01"
                               class="w-full px-3 py-2 mt-1 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                               x-model="form[field]" />
                    </div>
                </template>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Remarks</label>
                    <textarea rows="2" x-model="form.remarks"
                        class="w-full px-3 py-2 border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"></textarea>
                </div>
            </div>

            <div class="flex justify-end mt-6 space-x-3">
                <button type="button" @click="closeModal()" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">Cancel</button>
                <button type="button" @click="saveEntry()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Save</button>
            </div>
        </div>
    </x-modal>
</div>

    {{-- Submit Button --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('production-detail-reports.index') }}"
        class="px-4 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none">
            Back to List
        </a>
        <button type="submit"
                class="px-4 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{ $editing ? 'Update' : 'Save' }}
        </button>
    </div>

</form>
<script>
function productionEntries({ reportId }) {
    console.log('Report ID:', reportId); 
        return {
            showModal: false,
            entries: [],
            form: {
                id: null,
                production_date: '',
                production_time: '',
                temperature: '',
                beginning_weight: '',
                ending_weight: '',
                raw_material_weight_salang: '',
                finished_product_weight: '',
                quantity_made: '',
                reject_quantity: '',
                balance: '',
                remarks: ''
            },
            fieldLabels: {
                production_date: 'Production Date',
                production_time: 'Time',
                temperature: 'Temperature',
                beginning_weight: 'Beginning Weight',
                ending_weight: 'Ending Weight',
                raw_material_weight_salang: 'Raw Material per Salang',
                finished_product_weight: 'Finished Product Weight',
                quantity_made: 'Quantity Made',
                reject_quantity: 'Reject Quantity',
                balance: 'Balance'
            },
            
            openModal() {
                this.resetForm();
                this.showModal = true;
                this.$dispatch('open-modal', 'entry-modal');
            },
            
            closeModal() {
                this.showModal = false;
                this.$dispatch('close-modal', 'entry-modal');
            },
            
            editEntry(entry) {
                // console.log(entry);
                this.form = { ...entry };
                this.showModal = true;
                this.$dispatch('open-modal', 'entry-modal');
            },
            
            resetForm() {
                this.form = {
                    id: null,
                    production_date: '',
                    production_time: '',
                    temperature: '',
                    beginning_weight: '',
                    ending_weight: '',
                    raw_material_weight_salang: '',
                    finished_product_weight: '',
                    quantity_made: '',
                    reject_quantity: '',
                    balance: '',
                    remarks: ''
                };
            },
            
            async fetchEntries() {
                try {
                    const response = await fetch(`/production-detail-reports/${reportId}/entries`);
                    this.entries = await response.json();
                } catch (error) {
                    console.error('Error fetching entries:', error);
                }
            },
            
            async saveEntry() {
                try {
                    const isEdit = !!this.form.id;

                    console.log(isEdit ? 'Edit Mode: Updating entry...' : 'Create Mode: Storing new entry...');

                    const url = isEdit
                        ? `/production-detail-entries/${this.form.id}`
                        : `/production-detail-reports/${reportId}/entries`;

                    const method = isEdit ? 'PUT' : 'POST';

                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        console.log(isEdit ? 'Entry updated successfully.' : 'Entry created successfully.');
                        this.closeModal();
                        this.fetchEntries();
                    } else {
                        console.error('Failed to save entry.', await response.text());
                    }
                } catch (error) {
                    console.error('Error saving entry:', error);
                }
            },

            async deleteEntry(id) {
                if (confirm('Are you sure you want to delete this entry?')) {
                    try {
                        const response = await fetch(`/production-detail-entries/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });
                        
                        if (response.ok) {
                            this.fetchEntries();
                        }
                    } catch (error) {
                        console.error('Error deleting entry:', error);
                    }
                }
            }
        }
    }

    function jobOrderSelect(isEditMode) {
        return {
            isEditMode: isEditMode,

            query: isEditMode ? '' : @json(old('jo_no', $report->jo_no ?? '')),
            results: [],
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

            showResults: false,
            joItems: @json(old('joItems', $joItems ?? [])),
            selectedJoItem: @json(old('jo_item_id', $report->jo_item_id ?? '')),

            readyToUse: {{ old('is_ready_to_use', $report->is_ready_to_use ?? false) ? 'true' : 'false' }},
            ply: {{ old('ply', $report->ply ?? false) ? 'true' : 'false' }},
            metalInsert: {{ old('metal_insert', $report->metal_insert ?? false) ? 'true' : 'false' }},

            get selectedJoItemObj() {
                return this.joItems.find(item => item.id == this.selectedJoItem);
            },

            searchJO() {
                if (this.isEditMode || this.query.length < 2) {
                    this.results = [];
                    this.showResults = false;
                    return;
                }

                fetch(`/api/job-orders/search?q=${this.query}`)
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

                fetch(`/api/job-orders/${jo.id}/items`)
                    .then(res => res.json())
                    .then(items => {
                        this.joItems = items;
                        this.selectedJoItem = items.length ? items[0].id : '';
                    });
            }
        }
    }



</script>