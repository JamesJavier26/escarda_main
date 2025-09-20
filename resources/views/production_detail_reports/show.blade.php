<x-layouts.app>
    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('production-detail-reports.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">
            Production Detail Report
        </a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
             viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Report</span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    Production Detail Report
                </h1>
                <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Report ID: {{ $report->id }}</span>
                    <span class="mx-2">•</span>
                    <span>Created: {{ $report->created_at->format('M d, Y') }}</span>
                    @if($report->created_at != $report->updated_at)
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $report->updated_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('production-detail-reports.edit', $report->id) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-edit"></i> Edit Report
                </a>

                <a href="{{ route('production-detail-reports.index') }}"
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>

                @can('delete', $report)
                    <form action="{{ route('production-detail-reports.destroy', $report->id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2"
                                onclick="return confirm('Are you sure you want to delete this report?')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Basic Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">JO Number</p>
                    <p class="font-medium">{{ $report->jo_no }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Press</p>
                    <p class="font-medium">{{ $report->press ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Production Date</p>
                    <p class="font-medium">{{ $report->date_production?->format('Y-m-d') ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                    <p class="font-medium">{{ $report->pdr_status }}</p>
                </div>
            </div>
        </div>

        <!-- Material Information -->
<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
    <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-200">Material Information</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Ply Section -->
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Ply:</p>
                <span class="font-medium px-2 py-1 rounded text-sm 
                    {{ $report->ply ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $report->ply ? 'Yes' : 'No' }}
                </span>
            </div>
            
            @if($report->ply && $report->ply_types)
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Ply Types</p>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                        <ul class="space-y-2">
                            @php
                                $plyTypes = array_map('trim', explode(',', $report->ply_types));
                            @endphp

                            @foreach($plyTypes as $type)
                                @if(strtolower($type) !== 'others')
                                    <li class="flex items-start">
                                        <span class="flex items-center h-5">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($type) }}</span>
                                    </li>
                                @endif
                            @endforeach

                            @if($report->ply_type_others)
                                <li class="flex items-start">
                                    <span class="flex items-center h-5">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Others: {{ $report->ply_type_others }}</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
        </div>

        <!-- Metal Insert Section -->
        <div class="space-y-3">
            <div class="flex items-center space-x-2">
                <p class="text-sm text-gray-500 dark:text-gray-400">Metal Insert:</p>
                <span class="font-medium px-2 py-1 rounded text-sm 
                    {{ $report->metal_insert ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $report->metal_insert ? 'Yes' : 'No' }}
                </span>
            </div>
            
            @if($report->metal_insert && $report->metal_insert_types)
                <div class="space-y-2">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Metal Insert Types</p>
                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                        <ul class="space-y-2">
                            @php
                                $metalTypes = array_map('trim', explode(',', $report->metal_insert_types));
                            @endphp

                            @foreach($metalTypes as $type)
                                <li class="flex items-start">
                                    <span class="flex items-center h-5">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $type)) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    @if($report->metal_insert_details)
                        <div class="mt-3">
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Details</p>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $report->metal_insert_details }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Job Order Material Info -->
        <div class="col-span-1 md:col-span-2 mt-6">
            @php
                $joItem = $report->jobOrder->jo_items->first();
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Item</p>
                    <p class="font-medium">{{ $joItem?->item_name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Material</p>
                    <p class="font-medium">{{ $joItem?->material?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Hardness</p>
                    <p class="font-medium">{{ $joItem?->hardness ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Thickness</p>
                    <p class="font-medium">{{ $joItem?->thickness ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>



        <!-- Production Entries -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Production Entries</h3>
            </div>

            <div class="overflow-x-auto rounded shadow-md">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        <tr>
                            <x-table-column class="w-16 text-center">#</x-table-column>
                            <x-table-column class="text-left">Date</x-table-column>
                            <x-table-column class="text-right">Start Time</x-table-column>
                            <x-table-column class="text-right">Temp</x-table-column>
                            <x-table-column class="text-right">Begin Wt</x-table-column>
                            <x-table-column class="text-right">End Wt</x-table-column>
                            <x-table-column class="text-right">Daily Output</x-table-column>
                            <x-table-column class="text-right">Rejects</x-table-column>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($report->entries as $idx => $entry)
                            <tr>
                                <td class="text-center px-4 py-3">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 text-left">{{ $entry->production_date }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->start_time }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->temp_setting }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->beginning_weight_rawmat }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->ending_weight_rawmat }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->daily_output }}</td>
                                <td class="px-4 py-3 text-right">{{ $entry->reject_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center px-4 py-6 text-gray-500 dark:text-gray-400">
                                    No entries added yet
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Production Summary -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Production Summary</h2>
            @php
                $totalDailyOutput = $report->entries->sum('daily_output');
                $totalRejects = $report->entries->sum('reject_quantity');
                $uniqueDays = $report->entries->groupBy('production_date')->count();
                $beginWeight = (float) ($report->entries->first()?->beginning_weight_rawmat ?? 0);
                $endWeight   = (float) ($report->entries->last()?->ending_weight_rawmat ?? 0);
                $totalUsed   = $beginWeight - $endWeight;

                // Get unit short_name from the first entry (assuming all entries use the same unit)
                $unitShortName = $report->entries->first()?->unit?->short_name ?? '';
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Fabrication Days</p>
                    <p class="text-2xl font-bold">{{ $uniqueDays }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Total Daily Output</p>
                    <p class="text-2xl font-bold">{{ $totalDailyOutput }} {{ $unitShortName }}</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <p class="text-sm text-gray-500">Total Rejects</p>
                    <p class="text-2xl font-bold">{{ $totalRejects }} {{ $unitShortName }}</p>
                </div>
            </div>
        </div>



        <!-- Remarks -->
        @if($report->remarks)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
                <h2 class="text-lg font-semibold mb-2 text-gray-700 dark:text-gray-300">Remarks</h2>
                <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $report->remarks }}</p>
            </div>
        @endif
    </div>

    <!-- Print -->
    <div class="container mx-auto px-4 pb-8">
        <div class="flex justify-start">
            <a href="{{ route('reports.pdr', ['id' => $report->id]) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-file-pdf"></i> Print
            </a>
        </div>
    </div>
</x-layouts.app>
