<x-layouts.app>
<!-- Marquee Banner -->
<div class="bg-gradient-to-r from-red-600 to-red-800 dark:from-red-700 dark:to-red-900 text-white py-3 px-4 mb-6 rounded-lg shadow-sm">
    <marquee behavior="scroll" direction="left" class="text-lg font-semibold flex items-center">
        <span>Smile, God Loves You!</span>
    </marquee>
</div>


    <!-- Dashboard Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ __('Dashboard')}}</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">{{ __('Welcome to the dashboard') }}</p>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Users Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">0</p>
                    <p class="text-xs text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        {{ __('0% increase') }}
                    </p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 p-3 rounded-full">
                    <i class="fas fa-users text-blue-500 dark:text-blue-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Revenue') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">₱0</p>
                    <p class="text-xs text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        {{ __('0% increase') }}
                    </p>
                </div>
                <div class="bg-green-100 dark:bg-green-900 p-3 rounded-full">
                    <i class="fas fa-peso-sign text-green-500 dark:text-green-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Orders') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">0</p>
                    <p class="text-xs text-red-500 flex items-center mt-1">
                        <i class="fas fa-arrow-down text-xs mr-1"></i>
                        {{ __('0% decrease') }}
                    </p>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900 p-3 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-500 dark:text-purple-300 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Clients Card -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Clients') }}</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1">0</p>
                    <p class="text-xs text-green-500 flex items-center mt-1">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        {{ __('0% increase') }}
                    </p>
                </div>
                <div class="bg-orange-100 dark:bg-orange-900 p-3 rounded-full">
                    <i class="fas fa-user-tie text-orange-500 dark:text-orange-300 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Additional Dashboard Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
       
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border
                    border-gray-200 dark:border-gray-700 col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">
                {{ __('For Approval PE') }}
            </h2>

            <div class="space-y-4">
                @forelse ($pending_estimates as $pe)
                    <div class="flex items-start">
                        {{-- icon --}}
                        <div class="bg-yellow-100 dark:bg-yellow-900 p-2 rounded-full mr-3">
                            <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-300"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        {{-- text --}}
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $pe->pe_no }}
                                <span
                                    class="ml-2 inline-block px-2 py-0.5 text-[10px] rounded
                                        bg-yellow-200 dark:bg-yellow-700
                                        text-yellow-800 dark:text-yellow-100 uppercase tracking-wider">
                                    {{ $pe->pe_status }}
                                </span>
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $pe->salesRep->username ?? '—' }}
                                &nbsp;–&nbsp;{{ $pe->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('No pending price estimates.') }}
                    </p>
                @endforelse
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700 col-span-2">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Recent Activity') }}</h2>
            <div class="space-y-4">
                <!-- <div class="flex items-start">
                    <div class="bg-blue-100 dark:bg-blue-900 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-blue-500 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">New user registered</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">John Doe - 2 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-green-100 dark:bg-green-900 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-green-500 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Order #1234 completed</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">5 hours ago</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-purple-100 dark:bg-purple-900 p-2 rounded-full mr-3">
                        <svg class="h-5 w-5 text-purple-500 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Product updated</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Admin - Yesterday</p>
                    </div>
                </div> -->
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">{{ __('Quick Actions') }}</h2>
            <div class="space-y-3">
                <button class="w-full flex items-center justify-between px-4 py-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/50 transition-colors">
                    <span>Add New User</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-2 bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-300 rounded-lg hover:bg-green-100 dark:hover:bg-green-900/50 transition-colors">
                    <span>Create Price Estimate</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-2 bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors">
                    <span>Generate Report</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </button>
                <button class="w-full flex items-center justify-between px-4 py-2 bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300 rounded-lg hover:bg-orange-100 dark:hover:bg-orange-900/50 transition-colors">
                    <span>View Analytics</span>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Inspirational Quote -->
    <!-- <div class="bg-gradient-to-r from-indigo-500 to-blue-600 dark:from-indigo-700 dark:to-blue-800 text-white p-6 rounded-lg shadow-sm mb-6">
        <blockquote class="text-center">
            <p class="text-xl italic mb-2">"Every day may not be good, but there's something good in every day."</p>
            <footer class="text-sm opacity-80">— Alice Morse Earle</footer>
        </blockquote>
    </div> -->
</x-layouts.app>