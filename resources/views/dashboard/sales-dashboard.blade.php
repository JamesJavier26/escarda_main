<x-layouts.app title="Sales Dashboard">
    <!-- Marquee Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-700 dark:to-blue-900 text-white py-3 px-4 mb-6 rounded-lg shadow-sm">
        <marquee behavior="scroll" direction="left" class="text-lg font-semibold flex items-center">
            <span>Smile, God Loves You! • Today is {{ now()->format('l, F j, Y') }} • Hit your sales targets today!</span>
        </marquee>
    </div>

    <!-- Dashboard Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Sales Dashboard')}}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Sales performance and customer insights') }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full">
                <i class="fas fa-calendar-day mr-1"></i>
                {{ now()->format('M j, Y') }}
            </div>
            <div class="text-sm bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full">
                <i class="fas fa-bullseye mr-1"></i>
                Target: ₱{{ number_format($monthlyTarget, 2) }}
            </div>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Monthly Sales -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Monthly Sales') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">₱{{ number_format($monthlySales, 2) }}</p>
                    <p class="text-xs {{ $salesChange >= 0 ? 'text-green-500' : 'text-red-500' }} flex items-center mt-1">
                        <i class="fas {{ $salesChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }} text-xs mr-1"></i>
                        {{ abs($salesChange) }}% {{ $salesChange >= 0 ? 'increase' : 'decrease' }} vs last month
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-chart-line text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Target Achievement -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Target Achievement') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ number_format($targetAchievement, 1) }}%</p>
                    <p class="text-xs {{ $targetAchievement >= 100 ? 'text-green-500' : 'text-yellow-500' }} flex items-center mt-1">
                        <i class="fas {{ $targetAchievement >= 100 ? 'fa-check-circle' : 'fa-exclamation-circle' }} text-xs mr-1"></i>
                        {{ $targetAchievement >= 100 ? 'Target achieved' : 'Target pending' }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-bullseye text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- New Customers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('New Customers') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ $newCustomersCount }}</p>
                    <p class="text-xs text-purple-500 flex items-center mt-1">
                        <i class="fas fa-user-plus text-xs mr-1"></i>
                        {{ $newCustomersThisMonth }} this month
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <i class="fas fa-users text-purple-500 dark:text-purple-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Quote Conversion') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">{{ number_format($conversionRate, 1) }}%</p>
                    <p class="text-xs {{ $conversionRate >= 30 ? 'text-green-500' : 'text-orange-500' }} flex items-center mt-1">
                        <i class="fas {{ $conversionRate >= 30 ? 'fa-thumbs-up' : 'fa-chart-pie' }} text-xs mr-1"></i>
                        {{ $conversionRate >= 30 ? 'Strong' : 'Needs improvement' }}
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <i class="fas fa-percentage text-orange-500 dark:text-orange-300 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6" x-data="salesDashboard">
        <!-- Sales Charts Section -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    <i class="fas fa-chart-bar mr-2 text-blue-500"></i>
                    {{ __('Sales Performance') }}
                </h2>
                <div class="flex space-x-2">
                    <select x-model="timePeriod" @change="updateCharts" class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 px-2 py-1">
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>

            <!-- Tabs for different charts -->
            <div class="border-b border-gray-200 dark:border-gray-700 mb-4">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeChart = 'salesTrend'" :class="{
                        'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400': activeChart === 'salesTrend',
                        'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeChart !== 'salesTrend'
                    }" class="px-1 py-2 text-sm font-medium">
                        Sales Trend
                    </button>
                    <button @click="activeChart = 'productPerformance'" :class="{
                        'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400': activeChart === 'productPerformance',
                        'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeChart !== 'productPerformance'
                    }" class="px-1 py-2 text-sm font-medium">
                        Product Performance
                    </button>
                    <button @click="activeChart = 'customerSegmentation'" :class="{
                        'border-b-2 border-blue-500 text-blue-600 dark:text-blue-400': activeChart === 'customerSegmentation',
                        'border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300': activeChart !== 'customerSegmentation'
                    }" class="px-1 py-2 text-sm font-medium">
                        Customer Segmentation
                    </button>
                </nav>
            </div>

            <!-- Chart Containers -->
            <div class="space-y-6">
                <!-- Sales Trend Chart -->
                <div x-show="activeChart === 'salesTrend'" x-transition class="h-80">
                    <canvas id="salesTrendChart"></canvas>
                </div>

                <!-- Product Performance Chart -->
                <div x-show="activeChart === 'productPerformance'" x-transition class="h-80">
                    <canvas id="productPerformanceChart"></canvas>
                </div>

                <!-- Customer Segmentation Chart -->
                <div x-show="activeChart === 'customerSegmentation'" x-transition class="h-80">
                    <canvas id="customerSegmentationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Top Customers -->
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
                    <a href="{{ route('customers.create') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                        <span class="font-medium">Add New Customer</span>
                        <i class="fas fa-user-plus"></i>
                    </a>
                    <a href="{{ route('sales-orders.create') }}" class="block w-full flex items-center justify-between px-4 py-3 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                        <span class="font-medium">Create Sales Order</span>
                        <i class="fas fa-file-signature"></i>
                    </a>
                    <a href="" class="block w-full flex items-center justify-between px-4 py-3 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-colors">
                        <span class="font-medium">Generate Sales Report</span>
                        <i class="fas fa-file-pdf"></i>
                    </a>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                        <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                        {{ __('Top Customers') }}
                    </h2>
                    <a href="{{ route('customers.index') }}" class="text-sm text-blue-500 hover:underline">View All</a>
                </div>
        
                <div class="space-y-4">
                    @forelse($topCustomers as $customer)
                        <div class="flex items-center">
                            <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full mr-3">
                                <i class="fas fa-user-tie text-blue-500 dark:text-blue-300"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                    {{ $customer->name }}
                                </p>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $customer->sales_rep ? $customer->sales_rep->employee->full_name : 'Unassigned' }}
                                    </p>
                                    <p class="text-xs font-semibold text-green-600 dark:text-green-400">
                                        ₱{{ number_format($customer->total_spend, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No customer data available.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sales & Pending Approvals -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Sales Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class="fas fa-file-invoice-dollar mr-2 text-green-500"></i>
                    {{ __('Recent Sales Orders') }}
                </h2>
                <a href="{{ route('sales-orders.index') }}" class="text-sm text-blue-500 hover:underline">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SO #</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($recentSalesOrders as $order)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600 dark:text-blue-400">
                                <a href="{{ route('sales-orders.show', $order->id) }}">{{ $order->so_no }}</a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                {{ $order->customer->name ?? 'No Customer' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                       ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 
                                       'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No recent sales orders found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Estimates -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                    <i class="fas fa-clock mr-2 text-yellow-500"></i>
                    {{ __('Pending Estimates') }} (100)
                </h2>
                <a href="{{ route('pricing-estimates.index') }}" class="text-sm text-blue-500 hover:underline">View All</a>
            </div>

            <div class="space-y-4">
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
                            <span>Due: {{ $estimate->delivery_date }}</span>
                            <span class="mx-2">•</span>
                            <span>Amount: ₱{{ number_format($estimate->total_amount, 2) }}</span>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('pricing-estimates.show', $estimate->id) }}" class="text-blue-500 hover:text-blue-700 dark:hover:text-blue-400">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                    No pending estimates found
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Sales Team Performance -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex items-center">
                <i class="fas fa-medal mr-2 text-purple-500"></i>
                {{ __('Sales Team Performance') }}
            </h2>
            <select 
                x-model="performancePeriod" 
                @change="updatePerformanceChart"
                class="text-sm border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 px-2 py-1"
            >
                <option value="monthly">This Month</option>
                <option value="quarterly">This Quarter</option>
                <option value="yearly">This Year</option>
            </select>
        </div>
        
        <div class="h-80">
            <canvas id="teamPerformanceChart"></canvas>
        </div>
    </div>

    <!-- Alpine.js for interactive elements -->
    <script>
        document.addEventListener('alpine:init', () => {
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded. Charts will not render.');
                return;
            }
            Alpine.data('salesDashboard', () => ({
                activeChart: 'salesTrend',
                timePeriod: 'monthly',
                performancePeriod: 'monthly',
                salesTrendChart: null,
                productPerformanceChart: null,
                customerSegmentationChart: null,
                teamPerformanceChart: null,
                
                init() {
                    // Initialize charts
                    this.salesTrendChart = this.renderSalesTrendChart();
                    this.productPerformanceChart = this.renderProductPerformanceChart();
                    this.customerSegmentationChart = this.renderCustomerSegmentationChart();
                    this.teamPerformanceChart = this.renderTeamPerformanceChart();
                },
                
                updateCharts() {
                    // In a real app, you would fetch new data based on the time period
                    // Here we'll just update the existing charts with dummy data
                    this.salesTrendChart.data = this.getSalesTrendData();
                    this.salesTrendChart.update();
                    
                    this.productPerformanceChart.data = this.getProductPerformanceData();
                    this.productPerformanceChart.update();
                    
                    this.customerSegmentationChart.data = this.getCustomerSegmentationData();
                    this.customerSegmentationChart.update();
                },
                
                updatePerformanceChart() {
                    this.teamPerformanceChart.data = this.getTeamPerformanceData();
                    this.teamPerformanceChart.update();
                },
                
                renderSalesTrendChart() {
                    const ctx = document.getElementById('salesTrendChart').getContext('2d');
                    return new Chart(ctx, {
                        type: 'line',
                        data: this.getSalesTrendData(),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '₱' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '₱' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                renderProductPerformanceChart() {
                    const ctx = document.getElementById('productPerformanceChart').getContext('2d');
                    return new Chart(ctx, {
                        type: 'bar',
                        data: this.getProductPerformanceData(),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '₱' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '₱' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                renderCustomerSegmentationChart() {
                    const ctx = document.getElementById('customerSegmentationChart').getContext('2d');
                    return new Chart(ctx, {
                        type: 'doughnut',
                        data: this.getCustomerSegmentationData(),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const label = context.label || '';
                                            const value = context.raw || 0;
                                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                            const percentage = Math.round((value / total) * 100);
                                            return `${label}: ₱${value.toLocaleString()} (${percentage}%)`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                renderTeamPerformanceChart() {
                    const ctx = document.getElementById('teamPerformanceChart').getContext('2d');
                    return new Chart(ctx, {
                        type: 'bar',
                        data: this.getTeamPerformanceData(),
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return '₱' + context.raw.toLocaleString();
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '₱' + value.toLocaleString();
                                        }
                                    }
                                }
                            }
                        }
                    });
                },
                
                getSalesTrendData() {
                    // Sample data - replace with real data from your backend
                    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'];
                    const data = [500000, 750000, 600000, 900000, 1100000, 950000, 1200000];
                    
                    return {
                        labels: labels,
                        datasets: [{
                            label: 'Sales',
                            data: data,
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    };
                },
                
                getProductPerformanceData() {
                    // Sample data - replace with real data from your backend
                    return {
                        labels: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
                        datasets: [{
                            label: 'Sales Revenue',
                            data: [450000, 350000, 600000, 200000, 150000],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(139, 92, 246, 0.7)',
                                'rgba(244, 63, 94, 0.7)'
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 1)',
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(244, 63, 94, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                },
                
                getCustomerSegmentationData() {
                    // Sample data - replace with real data from your backend
                    return {
                        labels: ['Corporate', 'Government', 'Education', 'Healthcare', 'Retail'],
                        datasets: [{
                            label: 'Sales by Sector',
                            data: [1200000, 800000, 500000, 400000, 300000],
                            backgroundColor: [
                                'rgba(59, 130, 246, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(245, 158, 11, 0.7)',
                                'rgba(139, 92, 246, 0.7)',
                                'rgba(244, 63, 94, 0.7)'
                            ],
                            borderColor: [
                                'rgba(59, 130, 246, 1)',
                                'rgba(16, 185, 129, 1)',
                                'rgba(245, 158, 11, 1)',
                                'rgba(139, 92, 246, 1)',
                                'rgba(244, 63, 94, 1)'
                            ],
                            borderWidth: 1
                        }]
                    };
                },
                
                getTeamPerformanceData() {
                    // Sample data - replace with real data from your backend
                    return {
                        labels: ['John D.', 'Maria G.', 'Robert T.', 'Sarah L.', 'Michael B.'],
                        datasets: [{
                            label: 'Sales',
                            data: [1200000, 950000, 800000, 1100000, 700000],
                            backgroundColor: 'rgba(139, 92, 246, 0.7)',
                            borderColor: 'rgba(139, 92, 246, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Target',
                            data: [1000000, 1000000, 1000000, 1000000, 1000000],
                            backgroundColor: 'rgba(59, 130, 246, 0.5)',
                            borderColor: 'rgba(59, 130, 246, 1)',
                            borderWidth: 1,
                            type: 'line',
                            fill: false
                        }]
                    };
                }
            }));
        });
    </script>
</x-layouts.app>