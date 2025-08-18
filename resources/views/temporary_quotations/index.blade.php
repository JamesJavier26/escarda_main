<x-layouts.app title="Temporary Quotations">
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">Temporary Quotations</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
                Temporary Quotations
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage all temporary quotations here</p>
        </div>
        <a href="{{ route('temporary_quotations.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
            Create Temporary Quotation
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white dark:bg-gray-800 shadow rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Currency</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($temporaryQuotations as $quotation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $quotation->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $quotation->customer->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $quotation->currency->symbol ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $quotation->user->username ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                            {{ $quotation->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end">
                            <a href="{{ route('temporary_quotations.show', $quotation->id) }}"
                                class="flex items-center justify-center w-8 h-8 text-white bg-green-600 hover:bg-green-700 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500 transition mr-2"
                                title="View Temporary Quotation">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('temporary_quotations.edit', $quotation->id) }}"
                                class="flex items-center justify-center w-8 h-8 text-white bg-blue-600 hover:bg-blue-700 rounded-full focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                                title="Edit Temporary Quotation">
                                <i class="fas fa-pencil-alt text-sm"></i>
                            </a>
                        </td>  
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No temporary quotations found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.app>
