<x-layouts.app title="Price Estimate">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Pricing Estimates</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all pricing estimates</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
        <!-- Total Pricing Estimates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pricing Estimates</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $totalEstimates }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-file-invoice-dollar text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Draft Estimates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Draft Estimates</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $draftEstimates }}</p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-file-alt text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Estimates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Estimates</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $pendingEstimates }}</p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-500 dark:text-yellow-300 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        {{-- Filters & Actions --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex flex-col-reverse gap-4 md:flex-row md:items-end md:justify-between">
                <form method="GET" class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search estimates..."
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
                            class="px-3 py-2  rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                        <option value="">All Statuses</option>
                        @foreach (['pending', 'approved', 'canceled'] as $status)
                            <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>

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
                        <a href="{{ route('pricing-estimates.index') }}"
                            class="px-4 py-2  rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                            <i class="fas fa-times-circle"></i> 
                        </a>
                    @endif
                </form>


                {{-- <div class="flex justify-end">
                    <a href="{{ route('pricing-estimates.create') }}"
                    class="inline-flex items-center  px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus mr-1"></i>
                        Add New
                    </a>
                </div> --}}
            </div>
        </div>


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PE No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales Representative</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($pricingEstimates as $pe)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-md dark:bg-blue-900 dark:text-blue-200">
                                    {{ $pe->pe_no }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $pe->customer->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-800 dark:text-gray-200">
                                <div class="grid grid-cols-2 gap-x-2 gap-y-1">
                                    <span class="font-medium">Attn:</span><span>{{ $pe->attention }}</span>
                                    <span class="font-medium">Delivery:</span><span>{{ $pe->delivery_date }}</span>
                                    <span class="font-medium">Terms:</span><span>{{ $pe->terms }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200 text-center">{{ $pe->pe_items_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ optional($pe->salesRep->employee)->full_name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                <div class="flex flex-col">
                                    <span class="font-medium">{{ $pe->created_at->format('M j, Y') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Updated {{ $pe->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                <x-status-badge :status="$pe->pe_status">{{ ucfirst($pe->pe_status) }}</x-status-badge>
                                @php
                                    $status = $pe->pe_status;
                                    $hasMissingPrices = $pe->pe_items->contains(fn($item) => $item->unit_price === null || $item->unit_price == 0);
                                @endphp
                                @if ($hasMissingPrices && $pe->pe_status !== 'approved')
                                    <br/><span class="text-xs italic text-red-600 dark:text-red-400">No Unit Price</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                @if($pe->pe_status !== 'canceled')
                                <div class="flex justify-end items-center space-x-2">
                                    {{-- Price Entry Button --}}
                                    <a href="{{ route('pricing-estimates.price-entry', $pe->id) }}" 
                                    class="flex items-center justify-center w-8 h-8 text-white bg-indigo-600 hover:bg-indigo-700 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                                    title="Add Price Entry">
                                        <i class="fas fa-peso-sign text-sm"></i>
                                        <span class="sr-only">Add Price Entry</span>
                                    </a>

                                    {{-- Edit Button (Conditional) --}}
                                    @if($pe->sr_id == Auth::user()->id && $pe->pe_status !== 'approved')
                                        <a href="{{ route('pricing-estimates.edit', $pe->id) }}" 
                                        class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                        title="Edit Pricing Estimate">
                                            <i class="fas fa-pencil-alt text-sm"></i>
                                            <span class="sr-only">Edit</span>
                                        </a>
                                    @endif

                                    {{-- Delete Button --}}
                                    <form action="{{ route('pricing-estimates.destroy', $pe) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pricing estimate?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="flex items-center justify-center w-8 h-8 text-white bg-red-600 hover:bg-red-700 rounded-full focus:outline-none focus:ring-2 focus:ring-red-500 transition"
                                                title="Delete Pricing Estimate">
                                            <i class="far fa-trash-alt text-sm"></i>
                                            <span class="sr-only">Delete</span>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr><td class="px-6 py-4 whitespace-nowrap text-sm text-center" colspan="8">No record found</td></tr>
                    @endforelse
                </tbody>
            </table>
    <div class="px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-sm">

    {{-- Show Entries Dropdown --}}
    <div class="flex flex-wrap items-center gap-2">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <span class="text-gray-600 dark:text-gray-300">Show</span>
            <select name="perPage" id="perPage" onchange="this.form.submit()"
                class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none">
                @foreach ([10, 25, 50, 100] as $size)
                    <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>
                        {{ $size }}
                    </option>
                @endforeach
            </select>
            <span class="text-gray-600 dark:text-gray-300">entries</span>

            {{-- Preserve other filters --}}
            @foreach (request()->except(['perPage', 'page']) as $name => $value)
                <input type="hidden" name="{{ $name }}" value="{{ $value }}">
            @endforeach
        </form>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center md:justify-end">
        {{ $pricingEstimates->appends(request()->query())->links('vendor.pagination.tailwind') }}
    </div>
</div>


        </div>

    </div>
</x-layouts.app>
