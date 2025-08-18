<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('sales-orders.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Sales Orders</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Order</span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    Sales Order #{{ $salesOrder->so_no }}
                </h1>
                <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                   <span>Created: {{ $salesOrder->created_at->format('M d, Y') }}</span>
                    @if($salesOrder->approval_date)
                        <span class="mx-2">•</span>
                        <span>Approval: {{ $salesOrder->approval_date->format('M d, Y') }}</span>
                    @endif
                    @if($salesOrder->created_at != $salesOrder->updated_at)
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $salesOrder->updated_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                @if(in_array($salesOrder->so_status, [null, 'draft']))
                <a href="{{ route('sales-orders.edit', $salesOrder->id) }}" 
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit Order
                </a>
                @endif
                <a href="{{ route('sales-orders.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to List
                </a>
                
                @if(auth()->user()->can('delete', $salesOrder))
                    <form action="{{ route('sales-orders.destroy', $salesOrder->id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2"
                                onclick="return confirm('Are you sure you want to delete this sales order?')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Order Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Customer</p>
                    <p class="font-medium">{{ $salesOrder->customer->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Attention</p>
                    <p class="font-medium">{{ $salesOrder->attention }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Sales Representative</p>
                    <p class="font-medium"> {{ optional($salesOrder->salesRep->employee)->full_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PR Number</p>
                    <p class="font-medium">{{ $salesOrder->pr_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">PO Number</p>
                    <p class="font-medium">{{ $salesOrder->po_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">JO Number</p>
                    <p class="font-medium">{{ $salesOrder->jo_no ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">VAT</p>
                    <p class="font-medium">{{ $salesOrder->tax_status ?? 'N/A' }}</p>

                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Currency</p>
                    <p class="font-medium">{{ $salesOrder->currency->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium">
                        @php $status = $salesOrder->so_status; @endphp
                        @if($status === 'approved')
                            <span class="text-green-600 dark:text-green-400">Approved</span>
                        @elseif($status === 'rejected')
                            <span class="text-red-600 dark:text-red-400">Rejected</span>
                        @else
                            <x-status-badge :status="$status">{{ ucfirst($status) }}</x-status-badge>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact & Delivery Information Section -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Contact & Delivery Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Contact Person</p>
                    <p class="font-medium">{{ $salesOrder->customer->contact_person ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Contact Number</p>
                    <p class="font-medium">{{ $salesOrder->customer->telno ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Delivery Date</p>
                    <p class="font-medium">{{ $salesOrder->delivery_date ?? 'Not specified' }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items Section -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Order Items</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Unit Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($salesOrder->jobOrder->jo_items as $item)
                        <tr>
                            <td class="px-6 py-4 w-100 text-sm font-medium text-gray-900 dark:text-gray-100 align-top">
                                <div class="break-words">
                                    {{ $item->item_name ?? 'N/A' }} <br />
                                    <span class="text-gray-500 dark:text-gray-400 text-sm">{{ $item->item_description }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                        </tr>
                        @endforeach
                        @php
                            $partialTotal = $salesOrder->jobOrder->jo_items->sum(function($item) {
                                return $item->unit_price * $item->quantity;
                            }); 

                            $vatRate = 0.12;

                            $vat = 0;
                            $vatableSales = 0;
                            $zeroRatedSales = 0;
                            $grandTotal = $partialTotal;

                            if ($salesOrder->tax_status === 'Zero Rated') {
                                $vat = 0;
                                $vatableSales = 0;
                                $zeroRatedSales = $partialTotal;
                                $grandTotal = $partialTotal;
                            } elseif ($salesOrder->tax_status === 'VAT Inclusive') {
                                $vatableSales = $partialTotal / (1 + $vatRate); // extract vatable amount
                                $vat = $partialTotal - $vatableSales; // VAT portion
                                $grandTotal = $partialTotal; // total already includes VAT
                            }
                        @endphp

                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">Total Vatable Sales</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ number_format($vatableSales, 2) }}</td>
                    </tr>

                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">Zero Rated Sales</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ number_format($zeroRatedSales, 2) }}</td>
                    </tr>

                    <tr class="bg-gray-50 dark:bg-gray-700">
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-gray-100">VAT (12%)</td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ number_format($vat, 2) }}</td>
                    </tr>

                    <tr class="bg-gray-100 dark:bg-gray-600">
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-gray-100">Total Sales Amount</td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-gray-100">{{ number_format($grandTotal, 2) }}</td>
                    </tr>

                    </tbody>
                </table>
            </div>
        </div>


        {{-- Remarks & Attachments --}}
        
        <div class="grid grid-cols-1 gap-6 w-full mb-6">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold text-gray-100 mb-4 border-b border-gray-600 pb-2">Remarks & Attachments</h2>

                {{-- Remarks --}}
                @if($salesOrder->remarks)
                <div class="mb-6">
                    <p class="text-sm font-medium text-gray-400">Remarks</p>
                    <p class="text-gray-100 whitespace-pre-line">
                        {{ $salesOrder->remarks ?? '—' }}
                    </p>
                </div>
                @endif
                {{-- Attachments Section --}}
                @if ($salesOrder->attachments && count($salesOrder->attachments))
                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Attachments</h4>
                    <div class="grid gap-3">
                        @foreach ($salesOrder->attachments as $file)
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


        @hasrole('super-admin|admin')
            @if(in_array($salesOrder->so_status, [null, 'pending']))
                <div 
                    x-data="salesOrderApproval({ 
                        approveUrl: '{{ route('job-orders.activate', $salesOrder->jobOrder->id) }}',
                        rejectUrl: '{{ route('sales-orders.reject', $salesOrder->id) }}'
                    })" 
                    class="flex justify-center space-x-4 mb-4"
                >
                    <!-- Disapprove Button -->
                    <button type="button"
                        @click="confirmAction('reject')"
                        class="px-4 py-2 rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none dark:bg-red-700 dark:hover:bg-red-800">
                        <i class="fa fa-times mr-1"></i> Disapprove
                    </button>

                    <!-- Approve Button -->
                    <button type="button"
                        @click="confirmAction('approve')"
                        class="px-4 py-2 rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none dark:bg-green-700 dark:hover:bg-green-800">
                        <i class="fa fa-check mr-1"></i> Approve
                    </button>

                    <a href="{{ route('reports.salesOrder', $salesOrder->so_no) }}" target="_blank"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg 
                            focus:outline-none focus:ring-2 focus:ring-blue-500 transition flex items-center gap-2"
                        title="Print Sales Order">
                        <i class="fas fa-print text-sm"></i>
                        Print
                    </a>
                </div>
            @endif
        @endhasrole



    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('salesOrderShow', () => ({
                isModalOpen: false,
                modalType: 'approve',
                
                openModal(type) {
                    this.modalType = type;
                    this.isModalOpen = true;
                },
                
                submitForm() {
                    if (this.modalType === 'reject') {
                        this.$refs.rejectionReason.value = document.getElementById('rejection-reason').value;
                    }
                }
            }));
        });
    </script>


    @endpush
        <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('salesOrderApproval', ({ approveUrl, rejectUrl }) => ({
            async confirmAction(action) {
                const isApprove = action === 'approve';
                const isReject = action === 'reject';

                const result = await Swal.fire({
                    title: isApprove ? 'Approve Sales Order?' : 'Reject Sales Order?',
                    text: isApprove
                        ? 'Are you sure you want to approve this Sales Order?'
                        : 'Are you sure you want to reject this Sales Order?',
                    icon: isApprove ? 'success' : 'error',
                    showCancelButton: true,
                    confirmButtonText: isApprove ? 'Yes, Approve' : 'Yes, Reject',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: isApprove ? '#16a34a' : '#dc2626',
                    cancelButtonColor: '#6b7280'
                });

                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Please wait...',
                    text: isApprove ? 'Approving Sales Order...' : 'Rejecting Sales Order...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const response = await fetch(isApprove ? approveUrl : rejectUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        const data = await response.json().catch(() => ({}));
                        throw new Error(data.message || 'Request failed.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: isApprove ? 'Sales Order approved.' : 'Sales Order rejected.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    setTimeout(() => {
                        window.location.reload();
                    }, 1600);

                } catch (err) {
                    console.error(err);
                    Swal.fire('Error', err.message || 'Something went wrong.', 'error');
                }
            }
        }));
    });
</script>
</x-layouts.app>