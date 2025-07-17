@php
    $editing = $sales_order->exists;
      $initialCustomer = $editing && $sales_order->customer ? $sales_order->customer->toJson() : 'null';
@endphp

<div x-data="">



    <form
        {{-- {{ $attributes->merge(['class' => 'space-y-6']) }} --}}
        class="space-y-6"
        method="POST"
        action="{{ $editing ? route('sales-orders.update', $sales_order) : route('sales-orders.store') }}"
    >
        @csrf
        @if($editing) @method('PUT') @endif

        <div class="grid grid-cols-1 gap-6 w-full">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                <div class="flex justify-between items-start mb-4 pb-4 border-b border-gray-600">
                <h2 class="text-xl font-semibold text-gray-100">Customer Details</h2>
                <span class="bg-blue-900 text-blue-100 text-xs font-medium px-2.5 py-1 rounded">S.O NO: #{{$sales_order->so_no}}</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Name</p>
                    <p class="text-gray-100">{{ $sales_order->customer->name }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">P.R No:</p>
                    <p class="text-gray-100 font-mono">#{{ $sales_order->pr_no }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Telephone</p>
                    <p class="text-gray-100">{{ $sales_order->customer->telno }}</p>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="space-y-3">
                    <div>
                    <p class="text-sm font-medium text-gray-400">Email</p>
                    <p class="text-gray-100">{{ $sales_order->customer->email }}</p>
                    </div>
                    
                    <div>
                    <p class="text-sm font-medium text-gray-400">Address</p>
                    <p class="text-gray-100">{{ $sales_order->customer->address }}</p>
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
                        Items
                    </h3>
                </div>
            </div>
            
            <div class="p-4 overflow-x-auto">
                <div>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <x-table-column field="item_number" class="w-16">#</x-table-column>
                                <x-table-column field="item_name">Item Name</x-table-column>
                                <x-table-column field="quantity" class="w-32 text-right">Qty</x-table-column>
                                <x-table-column field="price" class="w-32 text-right">Unit Price</x-table-column>
                                <x-table-column field="subtotal" class="w-32 text-right">Total Price</x-table-column>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($items as $index => $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $index + 1 }}</td>
                                {{-- <td class="px-4 py-3">
                                    <div class="text-sm text-gray-800 dark:text-gray-200">
                                        <div class="font-medium">{{ $item->item->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $item->item->description }}
                                        </div>
                                    </div>
                                </td> --}}
                                <td class="px-4 py-3">
                                    <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                        <!-- Editable Name -->
                                        <input 
                                            type="text" 
                                            name="items[{{ $loop->index }}][name]" 
                                            value="{{ $item->item->name }}" 
                                            class="w-full px-2 py-1 text-sm border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                        />

                                        <!-- Editable Description -->
                                        <textarea 
                                            name="items[{{ $loop->index }}][description]" 
                                            rows="2" 
                                            class="w-full px-2 py-1 text-xs border rounded dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600"
                                        >{{ $item->item->description }}</textarea>
                                    </div>
                                </td>

                                {{-- <td class="px-4 py-3 text-right">{{ $item->quantity }} {{ $item->unit->name ?? '' }}</td> --}}
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end space-x-1">
                                        <input 
                                            type="number" 
                                            name="items[{{ $loop->index }}][quantity]" 
                                            value="{{ $item->quantity }}" 
                                            class="w-20 px-2 py-1 text-sm text-right border rounded dark:bg-gray-700 dark:text-gray-100 dark:border-gray-600"
                                            min="0" step="any"
                                        />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            {{ $item->unit->name ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                            @php
                                $subtotal = $items->sum(function($item) {
                                    return $item->quantity * $item->unit_price;
                                });
                                $vat = $sales_order->is_vat ? $subtotal * 0.12 : 0;
                                $total = $subtotal + $vat;
                            @endphp
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Subtotal:</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">{{ number_format($subtotal, 2) }}</td>
                            </tr>
                            @if($sales_order->is_vat)
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">VAT (12%):</td>
                                <td class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">{{ number_format($vat, 2) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900 dark:text-white">{{ number_format($total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @if($editing)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Left Card: Other Information -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Other Information</h2>
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

                <div class="mt-4">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Remarks
                    </label>
                    <textarea id="remarks" name="remarks" rows="4"
                        placeholder="Add Remarks"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('remarks')
                        ])>{{ old('remarks', $sales_order->remarks) }}</textarea>
                    @error('remarks') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Right Card: Comments -->
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
        @endif
        <div style="margin-top: 50px;"></div>
        <div class="flex justify-end space-x-4">
            <a href="{{ route('quotations.index') }}"
               class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                      text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                      border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                Back to List
            </a>
            <a href="{{ route('reports.salesOrder', $sales_order->so_no) }}" target="_blank"
               class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                      text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                      border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                Print
            </a>
            <button type="submit"
                    class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                           bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Save Changes
            </button>
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
</div>
@if($editing)
<script>
    function submitJobOrderForm() {
        if (confirm('Approve and create Job Order #{{ $sales_order->jobOrder->jo_no }}?')) {
            document.getElementById('activate-job-order-form').submit();
        }
    }
</script>
@endif
<script>
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