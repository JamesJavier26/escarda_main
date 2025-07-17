<x-layouts.app>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Quotation #{{ $quote_no }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all items from P.E No.:{{ $pe->pe_no }}</p>
    </div>

    <x-data-table :data="$pe_items" searchable filterable selectable title="">
        {{-- Actions --}}
        <x-slot name="actions">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('pricing-estimates.create') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 6v6m0 0v6m0-6h6m-6 0H6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>
        </x-slot>

        {{-- Table Columns --}}
        <x-slot name="columns">
            <x-table-column sortable field="item_number">Item No.</x-table-column>
            <x-table-column field="item_name">Item Name</x-table-column>
            <x-table-column class="text-right">Actions</x-table-column>
        </x-slot>

        {{-- Table Rows --}}
        <x-slot name="rows">
            @foreach ($pe_items as $pe_item)
                <x-table-row selectable>
                    <x-table-cell>{{ $pe_item->item->number }}</x-table-cell>
                    <x-table-cell>{{ $pe_item->item->name }}</x-table-cell>

                    <x-table-cell class="text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="" title="Remove Item" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </a>
         
                        </div>
                    </x-table-cell>
                </x-table-row>
            @endforeach
        </x-slot>
    </x-data-table>
</x-layouts.app>
