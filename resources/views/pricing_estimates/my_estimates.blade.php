<x-layouts.app title="Price Estimate">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">My Pricing Estimates</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all pricing estimates you created</p>
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
                    <a href="{{ route('pricing-estimates.my') }}"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                        <i class="fas fa-times-circle"></i> 
                    </a>
                @endif
                </form>


                <div class="flex justify-end">
                    <a href="{{ route('pricing-estimates.create') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus mr-1"></i>
                        Add New
                    </a>
                </div>
            </div>
        </div>


        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">PE No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attention</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terms</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Items</th>
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $pe->attention }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $pe->delivery_date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $pe->terms }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">{{ $pe->pe_items_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                {{ $pe->created_at->format('M j, Y') }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                <x-status-badge :status="$pe->pe_status">{{ ucfirst($pe->pe_status) }}</x-status-badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                <div class="flex justify-end space-x-2">
        
                                    <a href="{{ route('pricing-estimates.show', $pe->id) }}"
                                    class="flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 transition"
                                    title="View Pricing Estimate">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if (!in_array($pe->pe_status, ['pending', 'approved','canceled']))
                                        <a href="{{ route('pricing-estimates.edit', $pe->id) }}"
                                        class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                        title="Edit Pricing Estimate">
                                            <i class="fas fa-pencil-alt text-sm"></i>
                                        </a>
                                    @endif
                                    @if (!in_array($pe->pe_status, ['approved','pending', 'canceled']))
                                        <button type="button"
                                            onclick="confirmCancel({{ $pe->id }})"
                                            class="flex items-center justify-center w-8 h-8 text-white bg-amber-500 hover:bg-amber-600 rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 transition"
                                            title="Cancel PE">
                                            <i class="fas fa-ban text-xs"></i>
                                        </button>

                                        {{-- Hidden Form --}}
                                        <form id="cancel-form-{{ $pe->id }}"
                                            action="{{ route('pricing-estimates.cancel', $pe->id) }}"
                                            method="POST"
                                            class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                    @endif
                                    <div x-data>
                                        <form action="{{ route('pricing-estimates.destroy', $pe->id) }}" method="POST" x-ref="deleteForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="Swal.fire({
                                                    title: 'Delete this Price Estimate?',
                                                    text: 'This action cannot be undone.',
                                                    icon: 'warning',
                                                    showCancelButton: true,
                                                    confirmButtonColor: '#e3342f',
                                                    cancelButtonColor: '#6b7280',
                                                    confirmButtonText: 'Yes, delete it!',
                                                    reverseButtons: true
                                                }).then((result) => {
                                                    if (result.isConfirmed) $refs.deleteForm.submit()
                                                })"
                                                class="w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white"
                                                title="Delete Price Estimate">
                                                <i class="far fa-trash-alt text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
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
<script>
function confirmCancel(id) {
    Swal.fire({
        title: 'Cancel Pricing Estimate?',
        text: 'Are you sure you want to cancel this PE? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Cancel it',
        cancelButtonText: 'No',
        customClass: {
            confirmButton: 'bg-red-600 text-white px-4 py-2 rounded mr-2',
            cancelButton: 'bg-gray-200 text-gray-800 px-4 py-2 rounded'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('cancel-form-' + id).submit();
        }
    });
}
</script>
</x-layouts.app>
