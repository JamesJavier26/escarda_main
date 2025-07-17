<x-layouts.app>
    <div class="mb-6 flex items-center text-sm">
        <a href="{{ route('dashboard') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Home</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <a href="{{ route('users.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Users</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
        <span class="text-gray-500 dark:text-gray-400">User Details</span>
    </div>

    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 tracking-tight">
                    {{ $user->name }}
                </h1>
                <div class="flex items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <span>Created: {{ $user->created_at->format('M d, Y') }}</span>
                    @if($user->created_at != $user->updated_at)
                        <span class="mx-2">•</span>
                        <span>Updated: {{ $user->updated_at->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Edit User
                </a>
                <a href="{{ route('users.index') }}" 
                   class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg shadow-sm transition-colors duration-200 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to List
                </a>
            </div>
        </div>

        <!-- User Information Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Basic Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 col-span-1">
                <div class="flex flex-col items-center mb-4">
                    <div class="w-24 h-24 avatar-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center mb-3 overflow-hidden">
                        <div class="flex items-center">
                            @if($user->avatar_url)
                                <img src="{{ asset('storage/'.$user->avatar_url) }}" alt="Profile Photo" class="w-full h-full object-cover">
                            @else
                                <span class="flex h-10 w-10 rounded-full items-center justify-center bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                    {{ $user->initials() }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                </div>

                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Status</p>
                        <p class="text-sm font-medium">
                            @if($user->is_active)
                                <span class="text-green-600 dark:text-green-400">Active</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Assigned Employee</p>
                        <p class="text-sm font-medium">
                            @if($user->employee)
                                {{ $user->employee->full_name }}
                                <span class="text-xs text-gray-400 block">{{ $user->employee->position->name }}</span>
                            @else
                                <span class="text-gray-400">Not assigned</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Roles</p>
                        <div class="flex flex-wrap gap-1 mt-1">
                            @forelse($user->roles as $role)
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-gray-400 text-sm">No roles assigned</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Details Card -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 col-span-1">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Account Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Username</p>
                        <p class="text-sm font-medium">{{ $user->username }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Email Verified</p>
                        <p class="text-sm font-medium">
                            @if($user->email_verified_at)
                                <span class="text-green-600 dark:text-green-400">Verified {{ $user->email_verified_at->format('M d, Y') }}</span>
                            @else
                                <span class="text-red-600 dark:text-red-400">Not verified</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last Login</p>
                        <p class="text-sm font-medium">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                                <span class="text-xs text-gray-400 block">IP: {{ $user->last_login_ip }}</span>
                            @else
                                <span class="text-gray-400">Never logged in</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Activity Log Card -->
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 col-span-1">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Recent Activity</h3>
                    <a href="#" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                </div>
                
                <div class="space-y-4">
                    <!-- Sample Activity 1 -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Password updated</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                        </div>
                    </div>
                    
                    <!-- Sample Activity 2 -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Profile information updated</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">1 day ago</p>
                        </div>
                    </div>
                    
                    <!-- Sample Activity 3 -->
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Successful login</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">IP: 192.168.1.1</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Section -->
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Additional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Department</p>
                    <p class="text-sm font-medium">{{ $user->employee->department->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Job Title</p>
                    <p class="text-sm font-medium">{{ $user->employee->position->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Phone Number</p>
                    <p class="text-sm font-medium">{{ $user->phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Timezone</p>
                    <p class="text-sm font-medium">{{ $user->timezone ?? config('app.timezone') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>