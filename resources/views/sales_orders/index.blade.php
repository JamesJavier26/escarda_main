<x-layouts.app title="Sales Orders">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Sales Orders</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all created sales orders</p>
    </div>

    {{-- Sales Order Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <!-- Total Sales Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sales Orders</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $totalSalesOrders ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-file-invoice text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Revenue This Month -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue This Month</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">₱{{ number_format($totalRevenue ?? 0, 2) }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-coins text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $pendingOrders ?? 0 }}</p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <i class="fas fa-clock text-orange-500 dark:text-orange-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved This Week -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved This Week</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $approvedThisWeek ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <i class="fas fa-check-circle text-purple-500 dark:text-purple-300 text-xl"></i>
                </div>
            </div>
        </div>

    </div>



    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">

        {{-- Filters & Actions --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex flex-col-reverse gap-4 md:flex-row md:items-end md:justify-between">
                <form method="GET" class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search sales orders..."
                            class="pl-10 pr-4 py-2 w-full md:w-64 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <select name="status"
                            class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                        <option value="">All Statuses</option>
                        @foreach (['draft', 'pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    <input type="date" name="from" value="{{ request('from') }}"
                        class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none"
                        placeholder="From">

                    <input type="date" name="to" value="{{ request('to') }}"
                        class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none"
                        placeholder="To">

                    <button type="submit"
                            class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                            <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search', 'status', 'from', 'to']))
                    <a href="{{ route('sales-orders.index') }}"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                        <i class="fas fa-times-circle"></i> 
                    </a>
                @endif
                </form>


                <div class="flex justify-end">
                    <a href="{{ route('sales-orders.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus mr-1"></i>
                        Add New
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">SO No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Delivery</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sales Rep</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">PO / PR</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date Ordered</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($salesOrders as $order)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <span class="font-medium">{{ $order->so_no }}</span><br/>
                                @if ($order->quotation)
                                    <span class="text-blue-600 text-xs font-semibold">
                                        <a href="#"
                                        onclick="openQuotePopup('{{ route('reports.quotations', $order->quotation->pricing_estimate->pe_no) }}'); return false;">
                                            Quote #{{ $order->quotation->quote_no }}
                                        </a>
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ optional($order->customer)->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ $order->delivery_date }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ optional($order->salesRep->employee)->full_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <div>PO: {{ $order->po_no ?? '—' }}</div>
                                <div>PR: {{ $order->pr_no ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($order->date_ordered)->format('M j, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <x-status-badge :status="$order->so_status">{{ ucfirst($order->so_status) }}</x-status-badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    @if($order->so_status == 'approved')
                                    <div class="relative inline-block text-left" x-data="{ open: false }">
                                        <button @click="open = !open"
                                            class="inline-flex items-center justify-center w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-sm font-medium text-gray-800 dark:text-gray-100 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 focus:outline-none">
                                            Export to
                                            <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <div x-show="open" @click.away="open = false"
                                            class="absolute right-0 z-50 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none"
                                            x-cloak>
                                            <div class="py-1">
                                                <a href="{{ route('reports.export-charge-invoice', ['order' => $order->id]) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Charge Invoice
                                                </a>
                                                <a href="{{ route('reports.export-billing-invoice', ['order' => $order->id]) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Billing Invoice
                                                </a>
                                                <a href="{{ route('reports.export-sales-invoice', ['order' => $order->id]) }}"
                                                    class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                    Sales Invoice
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <a href="{{ route('reports.salesOrder', $order->so_no) }}" target="_blank"
                                        class="flex items-center justify-center w-8 h-8 text-white bg-gray-600 hover:bg-gray-700 rounded-full focus:outline-none focus:ring-2 focus:ring-gray-500 transition"
                                        title="Print Sales Order">
                                        <i class="fas fa-print text-sm"></i>
                                    </a>
                                    {{-- View --}}
                                    <a href="{{ route('sales-orders.show', $order) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    {{-- Edit --}}
                                    @hasrole('super-admin')
                                        @if($order->so_status !== 'approved')
                                            <a href="{{ route('sales-orders.edit', $order) }}"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-green-600 hover:bg-green-700 text-white">
                                                <i class="fas fa-pencil-alt text-xs"></i>
                                            </a>
                                        @endif
                                    @endhasrole
                                  
                                    {{-- Delete --}}
                                    @hasrole('super-admin|admin|operation-manager')
                                    <form action="{{ route('sales-orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this Sales Order?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white">
                                            <i class="far fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                    @endhasrole

                                </div>
                                
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300" colspan="8">No records found</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Footer pagination & perPage --}}
            <div class="px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-sm">
                <div class="flex flex-wrap items-center gap-2">
                    <form method="GET" class="flex flex-wrap items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-300">Show</span>
                        <select name="perPage" onchange="this.form.submit()"
                            class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        <span class="text-gray-600 dark:text-gray-300">entries</span>

                        @foreach (request()->except(['perPage', 'page']) as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>

                <div class="flex justify-center md:justify-end">
                    {{ $salesOrders->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>

  
</x-layouts.app>

<script>
    function openQuotePopup(url) {
        const width = 1000;
        const height = 800;
        const left = (screen.width - width) / 2;
        const top = (screen.height - height) / 2;

        const popup = window.open(
            url,
            '_blank',
            `toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,resizable=yes,
             width=${width},height=${height},top=${top},left=${left}`
        );

        // Optional: Try focusing popup
        if (popup) {
            popup.focus();
        }
    }
</script>