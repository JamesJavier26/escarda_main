<x-layouts.app>
    <!-- Marquee Banner -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 dark:from-red-700 dark:to-red-900 text-white py-3 px-4 mb-6 rounded-lg shadow-sm">
        <marquee behavior="scroll" direction="left" class="text-lg font-semibold flex items-center">
            <span>Smile, God Loves You! • Today is {{ now()->format('l, F j, Y') }} • Have a productive day!</span>
        </marquee>
    </div>

    <!-- Dashboard Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard')}}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Key metrics and pending actions') }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ now()->format('M j, Y') }}
            </div>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Pending Estimates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Pending Estimates') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $pendingEstimatesCount }}</p>
                    <p class="text-xs text-yellow-500 flex items-center mt-1">
                        <i class="fas fa-clock text-xs mr-1"></i>
                        {{ $newEstimatesToday }} new today
                    </p>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900 p-3 rounded-full">
                    <i class="fas fa-file-invoice-dollar text-yellow-500 dark:text-yellow-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Quotations -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Approved Quotes') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $approvedQuotesCount }}</p>
                    <p class="text-xs text-green-500 flex items-center mt-1">
                        <i class="fas fa-check-circle text-xs mr-1"></i>
                        {{ $quotesReadyForPO }} ready for SO
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-file-signature text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Monthly Revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">₱{{ number_format($monthlyRevenue, 2) }}</p>
                    <p class="text-xs {{ $revenueChange >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center mt-1">
                        <i class="fas {{ $revenueChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                        {{ abs($revenueChange) }}% {{ $revenueChange >= 0 ? 'from' : 'below' }} last month
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-peso-sign text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Open Projects -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Production') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $recentProductionReports->count() }}</p>
                    <p class="text-xs text-orange-500 flex items-center mt-1">
                        <i class="fas fa-exclamation-circle text-xs mr-1"></i>
                        {{ $overdueProjects }} overdue
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <i class="fas fa-tasks text-orange-500 dark:text-orange-300 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" x-data="dashboard">
        <!-- Pending Approvals Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-clock mr-2 text-yellow-500"></i>
                    {{ __('Pending Approvals') }}
                </h2>
                <a href="#" class="text-sm text-blue-500 hover:underline">View All</a>
            </div>

            <div class="space-y-4">
                <!-- Tabs -->
                <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'estimates'" :class="{
                            'border-b-2 border-yellow-500 text-yellow-600 dark:text-yellow-400': activeTab === 'estimates',
                            'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'estimates'
                        }" class="px-1 py-2 text-sm font-medium">
                            Price Estimates ({{ $pendingEstimates->count() }})
                        </button>
                        <button @click="activeTab = 'quotations'" :class="{
                            'border-b-2 border-yellow-500 text-yellow-600 dark:text-yellow-400': activeTab === 'quotations',
                            'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'quotations'
                        }" class="px-1 py-2 text-sm font-medium">
                            Quotations ({{ $pendingQuotations->count() }})
                        </button>
                        <button @click="activeTab = 'jobOrders'" :class="{
                            'border-b-2 border-yellow-500 text-yellow-600 dark:text-yellow-400': activeTab === 'jobOrders',
                            'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeTab !== 'jobOrders'
                        }" class="px-1 py-2 text-sm font-medium">
                            Job Orders ({{ $pendingJobOrders->count() }})
                        </button>
                    </nav>
                </div>

                <!-- Estimates Tab Content -->
                <div x-show="activeTab === 'estimates'" x-transition class="space-y-4">
                    @forelse($pendingEstimates as $estimate)
                    <div class="flex items-start p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-2 rounded-full mr-3">
                            <i class="fas fa-file-invoice text-yellow-600 dark:text-yellow-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                {{ $estimate->pe_no }} - {{ $estimate->customer->name ?? 'No Customer' }}
                            </p>
                            <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>Sales Rep: {{ $estimate->salesRep->employee->full_name ?? 'Unassigned' }}</span>
                                <span class="mx-2">•</span>
                                <span>PR #: {{ $estimate->pr_no }}</span>
                                <span class="mx-2">•</span>
                                <span>Due: {{ $estimate->delivery_date }}</span>
                            </div>
                        </div>
                        <div class="relative ml-2">
                            <button @click="toggleMenu('estimate-{{ $estimate->id }}')" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openMenu === 'estimate-{{ $estimate->id }}'" 
                                 @click.away="openMenu = null"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                <div class="py-1">
                                    <a href="{{ route('pricing-estimates.show', $estimate->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">View Details</a>
                                    <a href="{{ route('pricing-estimates.edit', $estimate->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit</a>
                                    <a href="{{ route('quotations.create', ['pe_id' => $estimate->id]) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Create Quotation</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No pending estimates found
                    </div>
                    @endforelse
                </div>

                <!-- Quotations Tab Content -->
                <div x-show="activeTab === 'quotations'" x-transition class="space-y-4">
                    @forelse($pendingQuotations as $quotation)
                    <div class="flex items-start p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                        <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full mr-3">
                            <i class="fas fa-file-signature text-blue-600 dark:text-blue-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                {{ $quotation->quote_no }} - {{ $quotation->pricingEstimate->customer->name ?? 'No Customer' }}
                            </p>
                            <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                <span>Version: {{ $quotation->version }}</span>
                                <span class="mx-2">•</span>
                                <span>Date: {{ $quotation->quote_date->format('M d, Y') }}</span>
                                <span class="mx-2">•</span>
                                <span>Currency: {{ $quotation->currency->code }}</span>
                            </div>
                        </div>
                        <div class="relative ml-2">
                            <button @click="toggleMenu('quotation-{{ $quotation->id }}')" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openMenu === 'quotation-{{ $quotation->id }}'" 
                                 @click.away="openMenu = null"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                <div class="py-1">
                                    <a href="{{ route('quotations.show', $quotation->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">View Details</a>
                                    <a href="{{ route('quotations.edit', $quotation->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit</a>
                                    <a href="{{ route('quotations.download', $quotation->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Download PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No pending quotations found
                    </div>
                    @endforelse
                </div>

                <!-- Job Orders Tab Content -->
                <div x-show="activeTab === 'jobOrders'" x-transition class="space-y-4">
                    @forelse($pendingJobOrders as $jobOrder)
                    <div class="flex items-start p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg transition-colors">
                        <div class="bg-orange-100 dark:bg-orange-900 p-2 rounded-full mr-3">
                            <i class="fas fa-tasks text-orange-600 dark:text-orange-300"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                {{ $jobOrder->jo_no }} - {{ $jobOrder->salesOrder->customer->name ?? 'No Customer' }}
                            </p>
                            <div class="flex items-center mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{-- <span>Due: {{ $jobOrder->duedate?->format('M d, Y') ?? 'Not set' }}</span> --}}
                                <span class="mx-2">•</span>
                                <span>Status: {{ ucfirst($jobOrder->jo_status) }}</span>
                                <span class="mx-2">•</span>
                                <span>Created: {{ $jobOrder->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="relative ml-2">
                            <button @click="toggleMenu('jobOrder-{{ $jobOrder->id }}')" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div x-show="openMenu === 'jobOrder-{{ $jobOrder->id }}'" 
                                 @click.away="openMenu = null"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                <div class="py-1">
                                    {{-- <a href="{{ route('job-orders.show', $jobOrder->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">View Details</a> --}}
                                    <a href="{{ route('job-orders.edit', $jobOrder->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Edit</a>
                                    {{-- <a href="{{ route('job-orders.approve', $jobOrder->id) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Approve</a> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                        No pending job orders found
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <i class="fas fa-bolt mr-2 text-blue-500"></i>
                    {{ __('Quick Actions') }}
                </h2>
                <div class="space-y-3">
                    <a href="{{ route('pricing-estimates.create') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                        <span class="font-medium">Create Price Estimate</span>
                        <i class="fas fa-file-invoice-dollar"></i>
                    </a>
                    <a href="{{ route('items.create') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                        <span class="font-medium">Create Iten</span>
                        <i class="fas fa-file-signature"></i>
                    </a>
                    <a href="{{ route('customers.create') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                        <span class="font-medium">Add New Client</span>
                        <i class="fas fa-user-plus"></i>
                    </a>
                    {{-- <a href="{{ route('reports.index') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-colors">
                        <span class="font-medium">View Reports</span>
                        <i class="fas fa-chart-bar"></i>
                    </a> --}}
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 flex items-center">
                    <i class="fas fa-history mr-2 text-purple-500"></i>
                    {{ __('Recent Activity') }}
                </h2>
                <div class="space-y-4">
                    @foreach($recentActivities as $activity)
                    <div class="flex items-start">
                        <div class="{{ $activity['bg_color'] }} p-2 rounded-full mr-3">
                            <i class="{{ $activity['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['timestamp'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Production Reports -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                <i class="fas fa-clipboard-list mr-2 text-indigo-500"></i>
                {{ __('Recent Production Reports') }}
            </h2>
            {{-- <a href="{{ route('production-detail-reports.index') }}" class="text-sm text-blue-500 hover:underline">View All Reports</a> --}}
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">JO Number</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Production Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Item</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($recentProductionReports as $report)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                            <a href="{{ route('production-detail-reports.show', $report->id) }}">{{ $report->jo_no }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                            {{ $report->date_production?->format('M d, Y') ?? 'Not set' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                            {{ $report->item->name ?? 'No Item' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                            {{ number_format($report->quantity) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                Completed
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No recent production reports found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Alpine.js for interactive elements -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dashboard', () => ({
                activeTab: 'estimates',
                openMenu: null,
                
                init() {
                    // Set the default tab based on which has items
                    if (this.hasPendingEstimates) {
                        this.activeTab = 'estimates';
                    } else if (this.hasPendingQuotations) {
                        this.activeTab = 'quotations';
                    } else if (this.hasPendingJobOrders) {
                        this.activeTab = 'jobOrders';
                    }
                },
                
                get hasPendingEstimates() {
                    return {{ $pendingEstimates->count() }} > 0;
                },
                
                get hasPendingQuotations() {
                    return {{ $pendingQuotations->count() }} > 0;
                },
                
                get hasPendingJobOrders() {
                    return {{ $pendingJobOrders->count() }} > 0;
                },
                
                toggleMenu(menuId) {
                    this.openMenu = this.openMenu === menuId ? null : menuId;
                }
            }));
        });
    </script>
</x-layouts.app>