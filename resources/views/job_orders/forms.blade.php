@php
    $editing = $job_order->exists;
@endphp

<div x-data="">



    <form
        {{-- {{ $attributes->merge(['class' => 'space-y-6']) }} --}}
        class="space-y-6"
        method="POST"
        action="{{ $editing ? route('job-orders.update', $job_order) : route('sales-orders.store') }}"
    >
        @csrf
        @if($editing) @method('PUT') @endif

        @if(!$jo_no)
        <div class="grid grid-cols-1 gap-6 w-full">
            <p class="text-center">No Sales Order found.</p>
        </div>
        @else
        <div class="grid grid-cols-1 gap-6 w-full">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                <h2 class="text-xl font-semibold text-gray-100">Customer Details</h2>
                <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded">J.O NO: #{{$job_order->jo_no}}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Name</p>
                    <p class="text-gray-100">{{ $job_order->salesOrder->customer->name }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">P.R No:</p>
                    <p class="text-gray-100 font-mono">#{{ $job_order->salesOrder->pr_no }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Telephone</p>
                    <p class="text-gray-100">{{ $job_order->salesOrder->customer->telno }}</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Email</p>
                    <p class="text-gray-100">{{ $job_order->salesOrder->customer->email }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Address</p>
                    <p class="text-gray-100">{{ $job_order->salesOrder->customer->address }}</p>
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

                </div>
            </div>
            
            <div class="p-4 overflow-x-auto" >
                <div x-data="itemsTable()" x-on:item-added.window="receiveItems($event.detail)">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <x-table-column field="item_number" class="w-16">#</x-table-column>
                                <x-table-column field="item_name">Item Name</x-table-column>
                                <x-table-column field="specifications" class="w-64">Specifications</x-table-column>
                                <x-table-column field="quantity" class="w-32 text-right">Qty</x-table-column>
                                <x-table-column field="quantity" class="w-32 text-right">Linear</x-table-column>
                                
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($job_order->jo_items as $index => $jo_item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2 max-w-[50px] truncate">
                                        <div class="text-sm text-gray-800 dark:text-gray-200">
                                            <div class="font-medium">{{ $jo_item->item->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $jo_item->item->description }}</div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-2 text-sm">
                                            <!-- Rubber Material -->
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400">Rubber Material</label>
                                                <select name="items[{{ $index }}][rubber_material]"
                                                        class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700">
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}" {{ $material->id == $jo_item->material_id ? 'selected' : '' }}>
                                                            {{ $material->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Hardness -->
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400">Hardness/Duro</label>
                                                <input type="text" name="items[{{ $index }}][hardness]" value="{{ $jo_item->hardness }}"
                                                    class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700">
                                            </div>

                                            <!-- Thickness + Unit (Side by side) -->
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Thickness</label>
                                                <div class="flex space-x-2">
                                                    {{-- <select name="items[{{ $index }}][thickness]"
                                                            class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                                        <option value="">-- Select Thickness --</option>
                                                        @for ($i = 2; $i <= 25; $i++)
                                                            <option value="{{ $i }}" {{ $jo_item->thickness == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                    </select> --}}
                                                    <input type="text" name="items[{{ $index }}][thickness]" value="{{ $jo_item->thickness }}"
                                                    class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700">

                                                    <select name="items[{{ $index }}][thickness_unit]"
                                                            class="w-20 px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700 dark:text-white dark:border-gray-600">
                                                        <option value="mm" {{ $jo_item->thickness_unit == 'mm' ? 'selected' : '' }}>mm</option>
                                                        <option value="in" {{ $jo_item->thickness_unit == 'in' ? 'selected' : '' }}>in</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Mould -->
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400">Mould</label>
                                                <select name="items[{{ $index }}][mould]"
                                                        class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700">
                                                    @foreach ($moulds as $mould)
                                                        <option value="{{ $mould->id }}" {{ $mould->id == $jo_item->mould_id ? 'selected' : '' }}>
                                                            {{ $mould->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-right">{{ $jo_item->quantity }}</td>
                                    <td class="px-4 py-3" x-data="{ is_linear: {{ $jo_item->is_linear ? 'true' : 'false' }} }">
                                        <div class="space-y-2 text-sm">
                                            <!-- Is Linear Toggle -->
                                            <div class="flex items-center">
                                                <input type="hidden" name="items[{{ $index }}][is_linear]" value="0">
                                                <input type="checkbox" 
                                                    name="items[{{ $index }}][is_linear]" 
                                                    x-model="is_linear"
                                                    value="1"
                                                    {{ $jo_item->is_linear ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">Is Linear</span>
                                            </div>

                                            <!-- Total Length (conditionally shown) -->
                                            <div x-show="is_linear" x-transition>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400">Total Length</label>
                                                <input type="number" 
                                                    name="items[{{ $index }}][total_length]" 
                                                    value="{{ $jo_item->total_length }}"
                                                    class="w-full px-2 py-1 text-xs border rounded bg-gray-50 dark:bg-gray-700">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-gray-500 py-4">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-6">
            <div class="space-y-6">
                <!-- Timing & Remarks -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Timing & Remarks</h2>
                    
                    <div>
                        <x-forms.input 
                            label="Due Date" 
                            name="duedate" 
                            type="date" 
                            class="w-full"
                            :value="old('duedate', optional(\Carbon\Carbon::parse($job_order->duedate))->format('Y-m-d'))" 
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                        <textarea name="remarks" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="Any special notes or comments">{{ old('remarks', $job_order->remarks) }}</textarea>
                    </div>
                </div>

                <!-- Instructions & Delivery -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Instructions & Delivery</h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Work Instructions</label>
                        <textarea name="instructions" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="Specific instructions for production">{{ old('instructions', $job_order->instructions) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        @if($job_order->jo_status == 'delivered')
        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Delivery Details</h2>
                    <div>
                        {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Detail</label> --}}
                        <textarea name="delivery_details" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                            placeholder="">{{ old('delivery_details', $job_order->delivery_details ?? '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <div style="margin-top: 50px;"></div>
            <div class="flex items-center justify-between mt-10">
                {{-- LEFT SIDE BUTTONS: Back, Print, Save --}}
                <div class="flex space-x-4">
                    <a href="{{ route('job-orders.index') }}"
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                            text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                            border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                            focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-chevron-left mr-1"></i> Back to List
                    </a>
                    <a href="{{ route('reports.jobOrder',$job_order->jo_no) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium
                                text-white bg-blue-600 hover:bg-blue-700
                                border border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            <i class="fas fa-print"></i>
                            Print
                        </a>
                    <div x-data="{
                        isSubmitting: false,
                        confirmSubmit() {
                            Swal.fire({
                                title: 'Save Changes?',
                                text: 'Do you want to save these updates?',
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonColor: '#2563eb',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, Save'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    this.isSubmitting = true;
                                    $el.closest('form').submit();
                                }
                            });
                        }
                    }">
                        <button
                            type="button"
                            @click="confirmSubmit"
                            :disabled="isSubmitting"
                            class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                                bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <span x-show="!isSubmitting">Save Changes</span>
                            <span x-show="isSubmitting">Saving...</span>
                        </button>
                    </div>


                </div>

                {{-- RIGHT SIDE: Fake button that submits a separate form --}}
                @if($job_order->jo_status !== 'delivered')
                <div x-data="{
                    isSubmitting: false,
                    confirmDelivery() {
                        Swal.fire({
                            title: 'Mark as Delivered?',
                            text: 'This will update the job order status.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#16a34a',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, Deliver'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.isSubmitting = true;
                                document.getElementById('mark-delivered-form').submit();
                            }
                        });
                    }
                }">
                    <button
                        type="button"
                        @click="confirmDelivery"
                        :disabled="isSubmitting"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm shadow-sm">
                        <span x-show="!isSubmitting"><i class="fas fa-check-circle mr-1"></i> Mark as Delivered</span>
                        <span x-show="isSubmitting"><i class="fas fa-spinner fa-spin mr-1"></i> Delivering...</span>
                    </button>
                </div>
                @endif


            </div>

        @endif
    </form>
    @if($job_order->exists && $job_order->jo_status !== 'delivered')
        <form method="POST"
            id="mark-delivered-form"
            action="{{ route('job-orders.mark-delivered', $job_order) }}"
            class="hidden">
            @csrf
            @method('PATCH')
        </form>
    @endif

</div>
