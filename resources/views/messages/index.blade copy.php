<x-layouts.app>
    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar with conversations -->
        <div class="w-1/4 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Messages</h1>
                <div class="mt-3 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Search conversations..."
                        class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-200"
                    >
                </div>
            </div>

            <!-- Conversation list -->
            <div class="flex-1 overflow-y-auto">
                @foreach($users as $user)
                    @php
                        $conversation = App\Models\Conversation::firstOrCreateForUsers(auth()->id(), $user->id);
                        $latestMessage = $conversation->messages()->latest()->first();
                        $unreadCount = $conversation->unreadCount(auth()->id());
                    @endphp
                    <div
                        class="flex items-center p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition relative"
                        onclick="loadConversation({{ $conversation->id }}, {{ $user->id }})"
                    >
                        @if($unreadCount > 0)
                            <span class="absolute left-3 top-3 w-2 h-2 bg-blue-500 rounded-full"></span>
                        @endif
                        <div class="relative">
                            <img
                                src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}"
                                alt="{{ $user->username }}"
                                class="w-12 h-12 rounded-full object-cover"
                            >
                            <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"></span>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex justify-between items-center">
                                <h3 class="font-medium text-gray-800 dark:text-gray-200">{{ $user->username }}</h3>
                                @if($latestMessage)
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $latestMessage->created_at->diffForHumans() }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                @if($latestMessage)
                                    {{ $latestMessage->sender_id == auth()->id() ? 'You: ' : '' }}
                                    {{ Str::limit($latestMessage->content, 25) }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Main chat area -->
        <div class="flex-1 flex flex-col">
            <!-- Chat header -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex items-center">
                <div class="flex items-center">
                    <img
                        src="{{ $otherUser->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->username) }}"
                        alt="{{ $otherUser->username }}"
                        class="w-10 h-10 rounded-full object-cover"
                    >
                    <div class="ml-3">
                        <h3 class="font-medium text-gray-800 dark:text-gray-200">{{ $otherUser->username }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Online</p>
                    </div>
                </div>
                <div class="ml-auto flex space-x-2">
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </button>
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </button>
                    <button class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages area -->
            <div class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-700" id="messagesContainer">
                <div class="space-y-4">
                    @foreach($messages as $message)
                        <div class="flex items-start {{ $message->sender_id == auth()->id() ? 'justify-end' : '' }}">
                            @if($message->sender_id != auth()->id())
                                <img
                                    src="{{ $message->sender->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($message->sender->name) }}"
                                    alt="{{ $message->sender->name }}"
                                    class="w-8 h-8 rounded-full object-cover mr-3"
                                >
                            @endif
                            
                            <div class="{{ $message->sender_id == auth()->id() ? 'text-right' : '' }}">
                                <div class="{{ $message->sender_id == auth()->id() ? 'bg-blue-500 text-white' : 'bg-white dark:bg-gray-600' }} rounded-lg p-3 max-w-xs lg:max-w-md inline-block">
                                    <p>{{ $message->content }}</p>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $message->created_at->format('h:i A') }}
                                    @if($message->sender_id == auth()->id())
                                        • {{ $message->status }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Message input -->
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <form id="messageForm" class="flex items-center">
                    <button type="button" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 mr-2">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                        </svg>
                    </button>

                    <div class="flex-1 relative">
                        <input
                            type="text"
                            id="messageInput"
                            placeholder="Type a message..."
                            class="w-full py-2 px-4 rounded-full bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 text-gray-800 dark:text-gray-200"
                        >
                    </div>

                    <button type="submit" class="ml-2 bg-blue-500 text-white rounded-full p-2 hover:bg-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Initialize Echo with Reverb
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ config('broadcasting.connections.reverb.key') }}',
            wsHost: window.location.hostname,
            wsPort: 8080,
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });

        // Private channel subscription for 1:1 chats
        window.Echo.private(`user.{{ auth()->id() }}`)
            .listen('NewMessage', (e) => {
                if (!e.is_group) {
                    @this.loadMessages();
                    scrollToBottom();
                }
            });

        function loadConversation(conversationId, userId) {
            // In a real app, you would use Livewire or AJAX to load the conversation
            window.location.href = `/messages?conversation=${conversationId}&user=${userId}`;
        }

        function scrollToBottom() {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        }

        // Form submission
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const message = input.value.trim();

            if (message) {
                fetch('/messages', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        recipient_id: {{ $otherUser->id }},
                        content: message
                    })
                }).then(() => {
                    input.value = '';
                    scrollToBottom();
                });
            }
        });

        // Initial scroll to bottom
        scrollToBottom();
    </script>
    @endpush
</x-layouts.app>