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
                <p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
                <p class="font-medium">{{ $report->pdr_status}}</p>
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
        </div>
    </div>

    <!-- Salang Information -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Salang Information</h2>

        @php
            $salangs = $report->entries->groupBy('salang_number');
        @endphp

        @forelse($salangs as $salangNumber => $entries)
            <div class="mb-6 border-b pb-4 last:border-b-0 last:pb-0 last:mb-0">
                <h3 class="font-medium mb-3 text-gray-700 dark:text-gray-300">
                    Salang {{ $salangNumber }}
                </h3>

                @foreach($entries as $entry)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    @if($salangNumber == 1)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Beginning Weight</p>
                            <p class="font-medium">{{ $entry->beginning_weight ?? 'N/A' }} g</p>
                        </div>
                    @endif
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Temperature</p>
                            <p class="font-medium">{{ $entry->temperature ?? 'N/A' }} °C</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Time per Salang</p>
                            <p class="font-medium">{{ $entry->time_per_salang ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Weight per Salang</p>
                            <p class="font-medium">{{ $entry->weight_per_salang ?? 'N/A' }} g</p>
                        </div>
                    @if($salangNumber == 3)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Finished Product Weight</p>
                            <p class="font-medium">{{ $entry->finished_product_weight ?? 'N/A' }} g</p>
                        </div>
                    @endif
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Finished Product With Overflow</p>
                            <p class="font-medium">{{ $entry->finished_weight_with_overflow ?? 'N/A' }} g</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Finished Product Without Overflow</p>
                            <p class="font-medium">{{ $entry->finished_weight_without_overflow ?? 'N/A' }} g</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Checklist</p>
                            <p class="font-medium">
                                Size: {{ $entry->correct_size ? '✔' : '✖' }},
                                Color: {{ $entry->correct_color ? '✔' : '✖' }},
                                Hardness: {{ $entry->correct_hardness ? '✔' : '✖' }}
                            </p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Remarks</p>
                            <p class="font-medium">{{ $entry->remarks ?? 'N/A' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400">No salang data found</p>
        @endforelse
    </div>



    <!-- Production Entries -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-gray-700 dark:text-gray-300">Production Entries</h2>
                
        @php
        $totalDays = $report->entries->groupBy('production_date')->count();

        $totalQtyMade = $report->entries->reduce(function($carry, $entry) {
            return $carry + (is_numeric($entry->quantity_made) ? (float)$entry->quantity_made : 0);
        }, 0);

        $totalReject = $report->entries->reduce(function($carry, $entry) {
            return $carry + (is_numeric($entry->reject_quantity) ? (float)$entry->reject_quantity : 0);
        }, 0);

        $totalFinishedWeight = $report->entries->reduce(function($carry, $entry) {
            return $carry + (is_numeric($entry->finished_product_weight) ? (float)$entry->finished_product_weight : 0);
        }, 0);

        $totalSalangWeight = $report->entries->reduce(function($carry, $entry) {
            return $carry + (is_numeric($entry->weight_per_salang) ? (float)$entry->weight_per_salang : 0);
        }, 0);

        $overflow = $totalFinishedWeight - $totalSalangWeight;
        $efficiency = $totalSalangWeight > 0 ? round(($totalFinishedWeight / $totalSalangWeight) * 100, 2) : 0;
        @endphp

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Temp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Begin Wt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End Wt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty Made</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Reject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Finished Wt</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($report->entries as $entry)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $entry->production_date }}</td>
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
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300"></td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalQtyMade }} pcs</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $totalReject }} pcs</td>
                            <td class="px-6 py-3 text-sm text-gray-500 dark:text-gray-300">{{ $report->entries->first()->finished_product_weight }} g</td>
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
        $entries = $report->entries;

        // --- Initialize totals ---
        $totalRejects = 0;
        $totalRawMaterialWeight = 0;
        $totalMaterialsUsed = 0; // Actual materials used (beginning - ending)
        $totalSalangWeight = 0;
        $totalSalangWeightWithOverflow = 0;
        $totalSalangWeightWithoutOverflow = 0;
        $totalFinishedProductWeight = 0;

        // --- Iterate over salang numbers 1, 2, 3 ---
        for ($salangNumber = 1; $salangNumber <= 3; $salangNumber++) {
            $salangEntry = $entries->firstWhere('salang_number', $salangNumber);

            if ($salangEntry) {
                // Rejects
                $totalRejects += is_numeric($salangEntry->reject_quantity) ? (float)$salangEntry->reject_quantity : 0;

                // Raw material weight
                $totalRawMaterialWeight += isset($salangEntry->raw_material_weight_salang) 
                    ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->raw_material_weight_salang) 
                    : 0;

                // Actual materials used = beginning - ending (only from Salang 1)
                if ($salangNumber === 1) {
                    $beginning = isset($salangEntry->beginning_weight) 
                        ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->beginning_weight) 
                        : 0;
                    $ending = isset($salangEntry->ending_weight) 
                        ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->ending_weight) 
                        : 0;
                    $totalMaterialsUsed = $beginning - $ending;
                }

                // Weight per salang
                $totalSalangWeight += isset($salangEntry->weight_per_salang) 
                    ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->weight_per_salang) 
                    : 0;

                // Finished weights
                $totalSalangWeightWithOverflow += isset($salangEntry->finished_weight_with_overflow) 
                    ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->finished_weight_with_overflow) 
                    : 0;

                $totalSalangWeightWithoutOverflow += isset($salangEntry->finished_weight_without_overflow) 
                    ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->finished_weight_without_overflow) 
                    : 0;

                $totalFinishedProductWeight += isset($salangEntry->finished_product_weight) 
                    ? (float) preg_replace('/[^0-9.]/', '', $salangEntry->finished_product_weight) 
                    : 0;
            }
        }

        // Overflow = finished product - total salang weight
        $salangOverflow = $totalFinishedProductWeight - $totalSalangWeight;

        // Unique production days
        $uniqueDays = $entries->groupBy('production_date')->count();
    @endphp



        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Fabrication Days</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $uniqueDays }}</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Rejects</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalRejects }}</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Quantity Made</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $report->entries->sum(fn($entry) => is_numeric($entry->quantity_made) ? (float)$entry->quantity_made : 0) }} pcs</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Material Used</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalMaterialsUsed }} g</p>
            </div>

            {{-- Salang Totals --}}
            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Salang Quantity</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">
                    {{ $report->entries->sum(fn($entry) => is_numeric($entry->quantity_made) ? (float)$entry->quantity_made : 0) }} pcs
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Salang Weight</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalSalangWeight }} g</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total With Overflow</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalSalangWeightWithOverflow }} g</p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Without Overflow</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalSalangWeightWithoutOverflow }} g</p>
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

<div class="container mx-auto px-4 pb-8">
    <div class="flex justify-start">
        <a href="{{ route('reports.pdr', ['id' => $report->id]) }}" target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-md shadow-sm text-sm font-medium
                  text-white bg-green-600 hover:bg-green-700
                  border border-green-700 focus:outline-none focus:ring-2 focus:ring-green-400">
            <i class="fas fa-file-pdf"></i>
            Print
        </a>
    </div>
</div>
</x-layouts.app>