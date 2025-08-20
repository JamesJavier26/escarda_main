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
                <h2 class="text-xl font-semiboldtext-gray-900 dark:text-gray-100">Customer Details</h2>
                <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded">J.O NO: #{{$job_order->jo_no}}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Name</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $job_order->salesOrder->customer->name }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">P.R No:</p>
                    <p class="text-gray-900 dark:text-gray-100 font-mono">#{{ $job_order->salesOrder->pr_no }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Telephone</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $job_order->salesOrder->customer->telno }}</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Email</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $job_order->salesOrder->customer->email }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Address</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $job_order->salesOrder->customer->address }}</p>
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
            
            @php
                $isApproved = $job_order->jo_status === 'approved';
                // Field style (grayed out if approved, normal otherwise)
                $disabledClass = $isApproved 
                    ? 'bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300 cursor-not-allowed' 
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white';
            @endphp

            <div class="p-4 overflow-x-auto">
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
                                                <label class="block text-xs text-gray-600 dark:text-gray-400">Rubber Material</label>
                                                <select name="items[{{ $index }}][rubber_material]"
                                                        {{ $isApproved ? 'disabled' : '' }}
                                                        class="w-full px-2 py-1 text-xs border rounded {{ $disabledClass }}">
                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}" {{ $material->id == $jo_item->material_id ? 'selected' : '' }}>
                                                            {{ $material->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Hardness -->
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400">Hardness/Duro</label>
                                                <input type="text" 
                                                    name="items[{{ $index }}][hardness]" 
                                                    value="{{ $jo_item->hardness }}"
                                                    {{ $isApproved ? 'disabled' : '' }}
                                                    class="w-full px-2 py-1 text-xs border rounded {{ $disabledClass }}">
                                            </div>

                                            <!-- Thickness + Unit -->
                                            <div>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">Thickness</label>
                                                <div class="flex space-x-2">
                                                    <input type="text" 
                                                        name="items[{{ $index }}][thickness]" 
                                                        value="{{ $jo_item->thickness }}"
                                                        {{ $isApproved ? 'disabled' : '' }}
                                                        class="w-full px-2 py-1 text-xs border rounded {{ $disabledClass }}">

                                                    <select name="items[{{ $index }}][thickness_unit]"
                                                            {{ $isApproved ? 'disabled' : '' }}
                                                            class="w-20 px-2 py-1 text-xs border rounded {{ $disabledClass }}">
                                                        <option value="mm" {{ $jo_item->thickness_unit == 'mm' ? 'selected' : '' }}>mm</option>
                                                        <option value="in" {{ $jo_item->thickness_unit == 'in' ? 'selected' : '' }}>in</option>
                                                    </select>
                                                </div>
                                            </div>

                                            @php
                                                $serverMouldType = old("items.$index.mould_type")
                                                    ?? ($jo_item->new_mould_no || $jo_item->new_mould_name ? 'new' : 'old');
                                            @endphp

                                            <div x-data='{ mouldType: @json($serverMouldType) }' class="space-y-3">
                                                <label class="block text-xs text-gray-600 dark:text-gray-400">Mould</label>

                                                <!-- Toggle Old / New -->
                                                <div class="flex items-center space-x-4">
                                                    <label class="flex items-center space-x-1">
                                                        <input
                                                            type="radio"
                                                            name="items[{{ $index }}][mould_type]"
                                                            value="old"
                                                            x-model="mouldType">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">Old</span>
                                                    </label>

                                                    <label class="flex items-center space-x-1">
                                                        <input
                                                            type="radio"
                                                            name="items[{{ $index }}][mould_type]"
                                                            value="new"
                                                            x-model="mouldType">
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">New</span>
                                                    </label>
                                                </div>

                                                <!-- Old: Dropdown -->
                                                <div x-show="mouldType === 'old'" x-cloak>
                                                    <select
                                                        name="items[{{ $index }}][mould_id]"
                                                        {{ $isApproved ? 'disabled' : '' }}
                                                        class="w-full px-2 py-1 text-xs border rounded {{ $disabledClass }}">
                                                        <option value="">-- Select Mould --</option>
                                                        @foreach ($moulds as $mould)
                                                            <option value="{{ $mould->id }}" {{ $mould->id == $jo_item->mould_id ? 'selected' : '' }}>
                                                                {{ $mould->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    {{-- Hidden fallback if disabled --}}
                                                    @if ($isApproved)
                                                        <input type="hidden" name="items[{{ $index }}][mould_id]" value="{{ $jo_item->mould_id }}">
                                                    @endif
                                                </div>

                                                <!-- New: Inputs -->
                                                <div x-show="mouldType === 'new'" x-cloak class="space-y-3">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Mould No. <span class="text-red-500">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            name="items[{{ $index }}][new_mould_no]"
                                                            value="{{ old("items.$index.new_mould_no", $jo_item->new_mould_no) }}"
                                                            placeholder="Enter mould number"
                                                            :disabled="mouldType !== 'new' {{ $isApproved ? '|| true' : '' }}"
                                                            class="w-full px-3 py-1 rounded-lg border">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Name <span class="text-red-500">*</span>
                                                        </label>
                                                        <input
                                                            type="text"
                                                            name="items[{{ $index }}][new_mould_name]"
                                                            value="{{ old("items.$index.new_mould_name", $jo_item->new_mould_name) }}"
                                                            placeholder="Enter mould name"
                                                            :disabled="mouldType !== 'new' {{ $isApproved ? '|| true' : '' }}"
                                                            class="w-full px-3 py-1 rounded-lg border">
                                                    </div>
                                                </div>
                                            </div>



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
                                                    {{ $isApproved ? 'disabled' : '' }}
                                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 {{ $isApproved ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                <span class="ml-2 text-xs text-gray-600 dark:text-gray-400">Is Linear</span>
                                            </div>

                                            <!-- Total Length -->
                                            <div x-show="is_linear" x-transition>
                                                <label class="block text-xs text-gray-600 dark:text-gray-400">Total Length</label>
                                                <input type="number" 
                                                    name="items[{{ $index }}][total_length]" 
                                                    value="{{ $jo_item->total_length }}"
                                                    {{ $isApproved ? 'disabled' : '' }}
                                                    class="w-full px-2 py-1 text-xs border rounded {{ $disabledClass }}">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-gray-500 py-4">No items found.</td>
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

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>

                    @if(!empty($job_order->duedate))
                    <input
                        type="date"
                        name="duedate"
                        value="{{ \Carbon\Carbon::parse($job_order->duedate)->format('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                        {{ $job_order->jo_status === 'approved' ? 'disabled' : '' }}
                    >
                    @else
                    {{-- Show N/A when there is no due date --}}
                    <input
                        type="text"
                        value="N/A"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 cursor-not-allowed"
                        disabled
                    >
                    @endif
                </div>

                <!-- Remarks -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Remarks</label>
                    <textarea
                    name="remarks"
                    rows="3"
                    {{ $job_order->jo_status === 'approved' ? 'disabled' : '' }}
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="Any special notes or comments"
                    >{{ old('remarks', $job_order->remarks) }}</textarea>
                </div>
                </div>

                <!-- Instructions & Delivery -->
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Instructions & Delivery</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Work Instructions</label>
                    <textarea
                    name="instructions"
                    rows="3"
                    {{ $job_order->jo_status === 'approved' ? 'disabled' : '' }}
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-100"
                    placeholder="Specific instructions for production"
                    >{{ old('instructions', $job_order->instructions) }}</textarea>
                </div>
                </div>
            </div>
        </div>

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
                {{-- RIGHT SIDE: Action Buttons --}}
                <div x-data="{ isSubmitting: false }">
                    @if($job_order->jo_status === 'approved')
                       @if(!$job_order->is_delivered)
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

                        {{-- Hidden form for delivery --}}
                        <form id="mark-delivered-form" method="POST" action="{{ route('job-orders.mark-delivered', $job_order->id) }}" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
                    @endif

                    @elseif($job_order->jo_status === 'pending')
                        {{-- Cancel --}}
                        <div x-data="{
                            confirmCancel() {
                                Swal.fire({
                                    title: 'Cancel Job Order?',
                                    text: 'This will set the job order status to Cancelled.',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#6b7280',
                                    confirmButtonText: 'Yes, Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('cancel-form').submit();
                                    }
                                });
                            }
                        }" class="inline-block mr-2">
                            <button
                                type="button"
                                @click="confirmCancel"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm shadow-sm">
                                <i class="fas fa-times-circle mr-1"></i> Cancel
                            </button>
                        </div>

                        {{-- Hidden form for cancel --}}
                        <form id="cancel-form" method="POST" action="{{ route('job-orders.cancel', $job_order->id) }}" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>

                        {{-- Approve --}}
                        <div x-data="{
                            confirmApprove() {
                                Swal.fire({
                                    title: 'Approve Job Order?',
                                    text: 'This will set the job order status to Approved.',
                                    icon: 'question',
                                    showCancelButton: true,
                                    confirmButtonColor: '#16a34a',
                                    cancelButtonColor: '#d33',
                                    confirmButtonText: 'Yes, Approve'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        document.getElementById('approve-form').submit();
                                    }
                                });
                            }
                        }" class="inline-block">
                            <button
                                type="button"
                                @click="confirmApprove"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm shadow-sm">
                                <i class="fas fa-check mr-1"></i> Approve
                            </button>
                        </div>

                        {{-- Hidden form for approve --}}
                        <form id="approve-form" method="POST" action="{{ route('job-orders.approve', $job_order->id) }}" class="hidden">
                            @csrf
                            @method('PATCH')
                        </form>
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
