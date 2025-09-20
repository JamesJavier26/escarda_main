<x-layouts.app title="Temporary Quotation">
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('temporary_quotations.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Temporary Quotation</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">Edit Temporary Quotation</span>
    </div>

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">
         Edit Temporary Quotation
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Edit Temporary Quotation</p>
    </div>
    {{-- <x-alert type="info" class="mb-4">Fill‑in information for the new quotation.</x-alert> --}}

    @include('temporary_quotations.forms', [
        'temporary_quotation' => $temporary_quotation ?? new \App\Models\TemporaryQuotation(),
        'customers'           => $customers,
        'currencies'          => $currencies,
        'users'               => $users
    ])
</x-layouts.app>