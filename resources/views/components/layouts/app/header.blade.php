<!-- Header -->
<header class="bg-white dark:bg-gray-800 shadow-sm z-20 border-b border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between h-16 px-4">
        <!-- Left side: Logo and toggle -->
        <div class="flex items-center">
            <!-- Mobile menu button -->
            <button @click="toggleSidebar"
                class="p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Company Logo and Name -->
            <div class="flex items-center ml-4">
                <!-- Company Logo (replace with your actual logo) -->
                <div class="mr-3 flex items-center justify-center bg-blue-100 dark:bg-blue-900 rounded-full w-10 h-10">
                    <!-- <i class="fas fa-hospital text-blue-600 dark:text-blue-300 text-xl"></i> -->
                    <!-- Alternative: Use an actual image logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="Company Logo" class="h-8 w-8 rounded-full">
                </div>
                
                <div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 -mt-1">
                     Smile, God Loves You
                    </div>
                    <div class="font-bold text-xl text-gray-800 dark:text-white">
                        {{ config('app.name') }}
                    </div>
              
                </div>
            </div>
        </div>
        <!-- Right side: Search, notifications, profile -->
        <div class="flex items-center space-x-4">


            <!-- Notification Dropdown -->
            @php
                $user = auth()->user();
                $notifications = $user->notifications()->latest()->take(5)->get();
                $unreadCount = $user->notifications()->where('is_read', false)->count();
            @endphp


            <div id="toast-container" class="fixed top-4 right-4 space-y-2 z-50 flex flex-col items-end"></div>

            <!-- Notification Dropdown -->
            <div x-data="notificationDropdown(@js($notifications), {{ $unreadCount }})" x-init="init" class="relative">
                <!-- Notification Button with Badge -->
                <span class="relative inline-block">
                    <button @click="open = !open" class="p-2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </button>

                    <!-- Badge -->
                    <template x-if="unreadCount > 0">
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2"
                            x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                    </template>
                    <template x-if="unreadCount === 0">
                        <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full transform translate-x-1/2 -translate-y-1/2"></span>
                    </template>
                </span>

                <!-- Dropdown -->
            <div x-show="open" @click.away="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 w-80 max-h-[80vh] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50 overflow-hidden">

                <!-- Header -->
                <div class="px-4 py-3 text-sm font-semibold border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 flex justify-between items-center">
                    <span class="text-gray-800 dark:text-gray-100">Notifications</span>
                    <button @click="markAllAsRead" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors">
                        Mark all as read
                    </button>
                </div>

                <!-- Notification List -->
                <div class="overflow-y-auto" style="max-height: calc(80vh - 80px)">
                    <template x-if="notifications.length > 0">
                        <template x-for="notification in notifications.slice(0, 8)" :key="notification.id">
                            <div @click="onNotificationClick(notification)"
                                class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors border-b border-gray-100 dark:border-gray-700 last:border-b-0"
                                :class="{ 'bg-blue-50/50 dark:bg-gray-700/50': !notification.is_read }">
                                <div class="flex gap-3">
                                    <!-- Notification Icon -->
                                    <div class="flex-shrink-0 mt-1">
                                        <template x-if="notification.type === 'success'">
                                            <div class="h-8 w-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </template>
                                        <template x-if="notification.type === 'error'">
                                            <div class="h-8 w-8 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </div>
                                        </template>
                                        <template x-if="!notification.type || notification.type === 'info'">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <!-- Notification Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-start">
                                            <h3 class="font-medium text-gray-900 dark:text-gray-100" 
                                                :class="{ 'font-semibold': !notification.is_read }"
                                                x-text="notification.title"></h3>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2 whitespace-nowrap" 
                                                x-text="formatTime(notification.created_at)"></span>
                                        </div>
                                        
                                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1" 
                                        x-show="notification.message" 
                                        x-text="notification.message"></p>
                                        
                                        <!-- Attachment -->
                                        <template x-if="notification.attachment">
                                            <div class="mt-2 flex items-center gap-2 text-sm">
                                                <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <a :href="notification.attachment" 
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline truncate transition-colors" 
                                                target="_blank"
                                                x-text="notification.attachment.split('/').pop()"></a>
                                            </div>
                                        </template>
                                        
                                        <!-- Action Button -->
                                        <template x-if="notification.action_button">
                                            <div class="mt-2">
                                                <a :href="notification.action_button.url" 
                                                class="inline-block text-xs px-3 py-1 bg-blue-600 dark:bg-blue-700 text-white rounded-md hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors"
                                                x-text="notification.action_button.label ?? 'View'"></a>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">We'll notify you when something arrives.</p>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center">
                    <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline transition-colors">
                        View all notifications
                    </a>
                </div>
            </div>

            </div>

            <!-- Profile -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center focus:outline-none">
                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        <span
                            class="flex h-full w-full items-center justify-center rounded-lg bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                            {{ Auth::user()->initials() }}
                        </span>
                    </span>
                    <span class="ml-2 hidden md:block">{{ Auth::user()->username }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" :class="{ 'block': open, 'hidden': !open }"
                    class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700">
                    <a href="{{ route('settings.profile.edit') }}"
                        class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                        </div>
                    </a>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit"
                            class="block w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </div>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
    const USER_ID = @json(auth()->id());
    function notificationDropdown(initialNotifications, unreadCount) {
        return {
            open: false,
            notifications: initialNotifications,
            unreadCount: unreadCount,

            init() {
                window.Echo.private(`notifications.${USER_ID}`)
                    .listen('.notification.sent', (e) => {
                        if (!this.notifications.some(n => n.id === e.id)) {
                            this.notifications.unshift(e);
                            this.unreadCount++;
                            this.logNotificationReceived(e.id);
                            this.showToast(e.title || 'New Notification');
                        }
                    });
            },

            markAsRead(notificationId) {
                fetch(`/notifications/${notificationId}/mark-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({})
                }).then(response => {
                    if (response.ok) {
                        const noti = this.notifications.find(n => n.id === notificationId);
                        if (noti) noti.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                });
            },

            // ✅ New function for "Mark all as read"
            markAllAsRead() {
                fetch(`/notifications/mark-all-read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                }).then(response => {
                    if (response.ok) {
                        this.notifications.forEach(n => n.is_read = true);
                        this.unreadCount = 0;
                    }
                }).catch(err => {
                    console.error('Error marking all as read:', err);
                });
            },

            logNotificationReceived(id) {
                console.log('Notification received:', id);
            },

            onNotificationClick(notification) {
                this.markAsRead(notification.id);
                if (notification.action_button?.url) {
                    window.location.href = notification.action_button.url;
                }
            },

            showToast(message) {
                const container = document.getElementById('toast-container');

                const toast = document.createElement('div');
                toast.innerHTML = `
                    <div 
                        x-data="{ show: true }"
                        x-init="setTimeout(() => show = false, 3000)"
                        x-show="show"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="w-full max-w-xs rounded-lg shadow-lg border bg-white dark:bg-gray-800 border-blue-200 dark:border-blue-700 p-4 flex items-start gap-3"
                    >
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 11-10 10A10 10 0 0112 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 text-sm text-gray-800 dark:text-gray-100 font-medium">
                            ${message}
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                `;
                container.appendChild(toast);

                // Clean up after animation
                setTimeout(() => toast.remove(), 4000);
            },

            formatTime(isoString) {
                const date = new Date(isoString);
                const now = new Date();
                
                const isToday = date.toDateString() === now.toDateString();
                const options = { hour: '2-digit', minute: '2-digit' };

                if (isToday) {
                    return `Today, ${date.toLocaleTimeString([], options)}`;
                }

                const dateOptions = { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                return date.toLocaleString(undefined, dateOptions); // e.g. "Jun 29, 10:15 AM"
            }
        }
    }
</script>