<x-layouts.app title="Quotations">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Quotations</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all created quotations</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <!-- Quotations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Quotations</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $totalQuotations }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-file-invoice-dollar text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Quotation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved Quotation</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $approvedQuotation }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>
        <!-- Pending Quotation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Quotation</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $pendingQuotation }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-500 dark:text-yellow-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Disapproved Quotation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Disapproved Quotation</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $rejectQuotation }}</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900 p-3 rounded-full">
                    <i class="fas fa-times-circle text-red-500 dark:text-red-300 text-xl"></i>
                </div>
            </div>
        </div>

    </div>


    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">

        {{-- Filters & Actions --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex flex-col-reverse gap-4 md:flex-row md:items-end md:justify-between">
                <form method="GET" class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    {{-- Search --}}
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search..."
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

                    {{-- Status Filter --}}
                    <select name="status"
                        class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                        <option value="">All Statuses</option>
                        @foreach (['pending', 'approved', 'rejected'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ $status === 'rejected' ? 'Disapproved' : ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Date Filters --}}
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="px-3 py-2  rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none"
                        placeholder="From">

                    <input type="date" name="to" value="{{ request('to') }}"
                        class="px-3 py-2  rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none"
                        placeholder="To">

                    <button type="submit"
                        class="px-4 py-2  rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                        <i class="fas fa-filter"></i>
                    </button>

                    @if(request()->hasAny(['search', 'status', 'from', 'to']))
                        <a href="{{ route('quotations.index') }}"
                            class="px-4 py-2  rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                            <i class="fas fa-times-circle"></i>
                        </a>
                    @endif
                </form>

        
                {{-- @hasrole('admin|super-admin')
                <a href="{{ route('quotations.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Add New
                </a>
                @endhasrole --}}
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quote No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">P.E No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quote Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($quotations as $quote)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <span class="font-medium">{{ $quote->quote_no }}</span><br/>
                                @if($quote->salesOrder)
                                    <span class="text-blue-600 text-xs font-semibold">
                                        <a href="{{ route('sales-orders.edit', $quote->salesOrder) }}">SO #{{ $quote->salesOrder->so_no }}</a>
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ $quote->pricing_estimate->pe_no ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ $quote->pricing_estimate->customer->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ \Carbon\Carbon::parse($quote->quote_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <x-status-badge :status="$quote->quote_status">
                                    {{ $quote->quote_status === 'rejected' ? 'Disapproved' : ucfirst($quote->quote_status) }}
                                </x-status-badge>
                            </td>
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end items-center space-x-2">
                                    @hasrole('super-admin|admin')
                                    <a href="{{ route('quotations.edit', $quote) }}"
                                        class="flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                                        title="Edit Quotation">
                                        <i class="fas fa-pencil-alt text-sm"></i>
                                    </a>
                                    @endhasrole

                                    @if($quote->quote_status === 'rejected' && $quote->rejected_pdf_path)
                                            <!-- View Rejected PDF in Popup -->
                                            <a href="#"
                                            onclick="openQuotePopup('{{ Storage::url($quote->rejected_pdf_path) }}'); return false;"
                                            class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                            title="Print Quotation (Rejected)">
                                                <i class="fas fa-print text-sm"></i>
                                            </a>
                                        @elseif($quote->quote_status !== 'rejected')
                                            <!-- View Normal PDF in Popup -->
                                            <a href="#"
                                            onclick="openQuotePopup('{{ route('reports.quotations', $quote->pricing_estimate->pe_no) }}'); return false;"
                                            class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                            title="Print Quotation">
                                                <i class="fas fa-print text-sm"></i>
                                            </a>
                                    @endif

                                    {{-- @hasrole('super-admin') --}}
                                    {{-- <form action="{{ route('quotations.destroy', $quote) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center justify-center w-8 h-8 text-white bg-red-600 hover:bg-red-700 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 transition" title="Delete">
                                            <i class="far fa-trash-alt text-sm"></i>
                                        </button>
                                    </form> --}}
                                    {{-- @endhasrole --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No quotations found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination + PerPage Selector --}}
        <div class="px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-sm">
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <span class="text-gray-600 dark:text-gray-300">Show</span>
                    <select name="perPage" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none">
                        @foreach ([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-gray-600 dark:text-gray-300">entries</span>
                    @foreach (request()->except(['perPage', 'page']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            <div class="flex justify-center md:justify-end">
                {{ $quotations->appends(request()->query())->links('vendor.pagination.tailwind') }}
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
