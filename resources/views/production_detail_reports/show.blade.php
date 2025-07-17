<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('production-detail-reports.index')}}" class="text-blue-600 dark:text-blue-400 hover:underline">Production Detail Report</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">View Report</span>
    </div>

<div class="container mx-auto px-4 py-8">
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
    
    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
        <a href="{{ route('production-detail-reports.edit', $report->id) }}" 
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Edit Report
        </a>
        
        <a href="{{ route('production-detail-reports.index') }}" 
           class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to List
        </a>
        
        @if(auth()->user()->can('delete', $report))
            <form action="{{ route('production-detail-reports.destroy', $report->id) }}" method="POST" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2"
                        onclick="return confirm('Are you sure you want to delete this report?')">
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
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Basic Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">JO Number</p>
                <p class="font-medium">{{ $report->jo_no }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pressman</p>
                <p class="font-medium">{{ $report->user->username }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Press</p>
                <p class="font-medium">{{ $report->press ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Production Date</p>
                <p class="font-medium">{{ $report->date_production->format('Y-m-d') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
                <p class="font-medium">
                    @if($report->is_old_checked) Old @endif
                    @if($report->is_new_checked) New @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="font-medium">{{ $report->is_ready_to_use ? 'Ready to use' : 'In progress' }}</p>
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
                        {{ $report->ply ? 'bg-green-100 text-white-800 dark:bg-green-900 dark:text-white-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $report->ply ? 'Yes' : 'No' }}
                    </span>
                </div>
                
                @if($report->ply)
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Ply Types</p>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <ul class="space-y-2">
                                @foreach($report->ply_types as $type)
                                    <li class="flex items-start">
                                        <span class="flex items-center h-5">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($type) }}</span>
                                    </li>
                                @endforeach
                                @if($report->ply_type_others)
                                    <li class="flex items-start">
                                        <span class="flex items-center h-5">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Other: {{ $report->ply_type_others }}</span>
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
                        {{ $report->metal_insert ? 'bg-green-100 text-white-800 dark:bg-green-900 dark:text-white-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $report->metal_insert ? 'Yes' : 'No' }}
                    </span>
                </div>
                
                @if($report->metal_insert)
                    <div class="space-y-2">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Metal Insert Types</p>
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <ul class="space-y-2">
                                @foreach($report->metal_insert_types as $type)
                                    <li class="flex items-start">
                                        <span class="flex items-center h-5">
                                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
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
        </div>
    </div>

    <!-- Salang Information -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Salang Information</h2>
        
        @php
            $totalSalangQty = 0;
            $totalSalangWeight = 0;
            $totalSalangWeightWithOverflow = 0;
            $totalSalangWeightWithoutOverflow = 0;
        @endphp

        @for($i = 1; $i <= 3; $i++)
            @php
                $salangData = $report->{'salang_'.$i};
                $salang = is_array($salangData) ? $salangData : json_decode($salangData ?? '{}', true);
                
                if($salang && is_array($salang)) {
                    $totalSalangQty += $salang['quantity_made'] ?? 0;
                    $totalSalangWeight += $salang['finished_product_weight'] ?? 0;
                    $totalSalangWeightWithOverflow += $salang['finished_product_weight_with_overflow'] ?? 0;
                    $totalSalangWeightWithoutOverflow += $salang['finished_product_weight_without_overflow'] ?? 0;
                }
            @endphp

            @if(!empty($salang) && is_array($salang))
                <div class="mb-6 border-b pb-4 last:border-b-0 last:pb-0 last:mb-0">
                    <h3 class="font-medium mb-3 text-gray-700 dark:text-gray-300">Salang {{ $i }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Weight of raw materials</p>
                            <p class="font-medium">{{ $salang['raw_material_weight'] ?? 'N/A' }} g</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Temperature</p>
                            <p class="font-medium">{{ $salang['temperature'] ?? 'N/A' }} °C</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Press</p>
                            <p class="font-medium">{{ $salang['press'] ?? 'N/A' }}</p>
                        </div>
                        {{-- <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Pressman</p>
                            <p class="font-medium">{{ $salang['pressman'] ?? 'N/A' }}</p>
                        </div> --}}
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Weight per salang</p>
                            <p class="font-medium">{{ $salang['weight_per_salang'] ?? 'N/A' }} g</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Finished product weight with overflow</p>
                            <p class="font-medium">{{ $salang['finished_with_overflow'] ?? 'N/A' }} g</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Finished product weight without overflow</p>
                            <p class="font-medium">{{ $salang['finished_without_overflow'] ?? 'N/A' }} g</p>
                        </div>
                        {{-- <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Quantity Made</p>
                            <p class="font-medium">{{ $salang['quantity_made'] ?? 'N/A' }} pcs</p>
                        </div> --}}
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Remarks</p>
                            <p class="font-medium">{{ $salang['remarks'] ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Quality Checklist</p>
                            <div class="flex flex-wrap gap-4 mt-2">
                                <!-- Correct Size -->
                                <div class="flex items-center">
                                    <span class="mr-2 w-4 h-4 inline-flex items-center justify-center rounded border 
                                        {{ isset($salang['checklist']['correct_size']) && strtolower($salang['checklist']['correct_size']) === 'yes' 
                                            ? 'bg-green-500 border-green-600 dark:bg-green-600 dark:border-green-700' 
                                            : 'border-gray-300 dark:border-gray-500' }}">
                                        @if(isset($salang['checklist']['correct_size']) && strtolower($salang['checklist']['correct_size']) === 'yes')
                                            <svg class="w-3 h-3 text-white dark:text-gray-100" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Correct Size</span>
                                </div>

                                <!-- Correct Color -->
                                <div class="flex items-center">
                                    <span class="mr-2 w-4 h-4 inline-flex items-center justify-center rounded border 
                                        {{ isset($salang['checklist']['correct_color']) && strtolower($salang['checklist']['correct_color']) === 'yes' 
                                            ? 'bg-green-500 border-green-600 dark:bg-green-600 dark:border-green-700' 
                                            : 'border-gray-300 dark:border-gray-500' }}">
                                        @if(isset($salang['checklist']['correct_color']) && strtolower($salang['checklist']['correct_color']) === 'yes')
                                            <svg class="w-3 h-3 text-white dark:text-gray-100" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Correct Color</span>
                                </div>

                                <!-- Correct Hardness -->
                                <div class="flex items-center">
                                    <span class="mr-2 w-4 h-4 inline-flex items-center justify-center rounded border 
                                        {{ isset($salang['checklist']['correct_hardness']) && strtolower($salang['checklist']['correct_hardness']) === 'yes' 
                                            ? 'bg-green-500 border-green-600 dark:bg-green-600 dark:border-green-700' 
                                            : 'border-gray-300 dark:border-gray-500' }}">
                                        @if(isset($salang['checklist']['correct_hardness']) && strtolower($salang['checklist']['correct_hardness']) === 'yes')
                                            <svg class="w-3 h-3 text-white dark:text-gray-100" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Correct Hardness</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endfor

        @if($totalSalangQty > 0)
            <div class="mt-6 pt-4 border-t">
                <h3 class="text-md font-semibold mb-2 text-gray-700 dark:text-gray-300">Salang Totals</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Quantity</p>
                        <p class="font-medium">{{ $totalSalangQty }} pcs</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Weight</p>
                        <p class="font-medium">{{ $totalSalangWeight }} g</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total With Overflow</p>
                        <p class="font-medium">{{ $totalSalangWeightWithOverflow }} g</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Total Without Overflow</p>
                        <p class="font-medium">{{ $totalSalangWeightWithoutOverflow }} g</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Production Entries -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Production Entries</h2>
        
        @php
            $totalDays = $report->entries->groupBy('production_date')->count();
            $totalQtyMade = $report->entries->sum('quantity_made');
            $totalReject = $report->entries->sum('reject_quantity');
            $totalFinishedWeight = $report->entries->sum('finished_product_weight');
            $overflow = $totalFinishedWeight - $totalSalangWeight;
            $efficiency = $totalSalangWeight > 0 ? round(($totalFinishedWeight / $totalSalangWeight) * 100, 2) : 0;
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Temp (°C)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Begin Wt (g)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Wt (g)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Made (pcs)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reject (pcs)</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Finished Wt (g)</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($report->entries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->production_date }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->production_time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->temperature }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->beginning_weight }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->ending_weight }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->quantity_made }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->reject_quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->finished_product_weight }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No entries found</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($report->entries->count() > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700 font-medium">
                        <tr>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">Totals</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalDays }} days</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalQtyMade }} pcs</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalReject }} pcs</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalFinishedWeight }} g</td>
                        </tr>
                        <tr>
                            <td colspan="7" class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">Overflow (Entries - Salang)</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $overflow }} g</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Production Summary -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Production Summary</h2>
        
        @php
            // Calculate production metrics
            $uniqueDays = $report->entries->groupBy('production_date')->count();
            $totalHours = $report->entries->sum(function($entry) {
                return $entry->production_time ? (float)str_replace(':', '.', $entry->production_time) : 0;
            });
            $totalRejects = $report->entries->sum('reject_quantity');
            $totalQuantityMade = $report->entries->sum('quantity_made');
            
            // Calculate overflow
            $totalRawMaterialWeight = $report->entries->sum('raw_material_weight_salang');
            $totalWeightPerSalang = 0;
            for ($i = 1; $i <= 3; $i++) {
                $salang = $report->{'salang_'.$i} ?? []; // Get the array directly
                if (!empty($salang) && isset($salang['weight_per_salang'])) {
                    $totalWeightPerSalang += (float)$salang['weight_per_salang'];
                }
            }
            $overflow = $totalRawMaterialWeight + $totalWeightPerSalang;
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Fabrication Days</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $uniqueDays }}</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Hours</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($totalHours, 2) }}</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Rejects</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalRejects }}</p>
            </div>
            
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Quantity</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalQuantityMade }}</p>
            </div>
            
            {{-- <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Overflow</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($overflow, 2) }} g</p>
            </div> --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Material Used</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ number_format($overflow, 2) }} g</p>
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
</x-layouts.app>