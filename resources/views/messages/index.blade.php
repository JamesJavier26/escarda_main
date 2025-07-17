    <x-layouts.app>
        @php
            [$groups, $directs] = $conversations->partition(fn ($c) => $c->type === 'group');
        @endphp
        <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Sidebar -->
            <div x-data="chatSidebar()" class="w-1/4 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-100">Messages</h1>
                        <button @click="$dispatch('open-modal', 'create-convo')" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                    <!-- Search -->
                    <div class="mt-4 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="search"
                            x-model="search"
                            placeholder="Search messages"
                            class="w-full pl-10 pr-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-gray-800 dark:text-gray-200">
                    </div>
                </div>

                <!-- Chat list -->
                <div class="flex-1 overflow-y-auto">
                    {{-- GROUP CHATS --}}
                    <div class="p-2">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1 px-2">Group Chats</h3>
                        <template x-for="c in filteredGroups" :key="c.id">
                            <a :href="c.url"
                                class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="c.active ? 'bg-blue-50 dark:bg-blue-900' : ''">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2M7 20H2v-2a3 3 0 015.356-1.857" />
                                    </svg>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium" x-text="c.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="c.last"></p>
                                </div>
                                <span class="text-xs text-gray-400" x-text="c.time"></span>
                            </a>
                        </template>
                    </div>

                    {{-- DIRECT MESSAGES --}}
                    <div class="p-2">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase mb-1 px-2">Direct Messages</h3>
                        <template x-for="c in filteredDirects" :key="c.id">
                            <a :href="c.url"
                                class="flex items-center p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                :class="c.active ? 'bg-blue-50 dark:bg-blue-900' : ''">
                                <img :src="c.avatar" class="h-8 w-8 rounded-full" :alt="c.name">
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium" x-text="c.name"></p>
                                    <p class="text-xs text-gray-500 truncate" x-text="c.last"></p>
                                </div>
                                <span class="text-xs text-gray-400" x-text="c.time"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>


            <!-- Main chat area -->
            <div class="flex-1 flex flex-col">

                @if ($conversation)
                    <div x-data="chat({{ $conversation->id }}, '{{ csrf_token() }}', {{ auth()->id() }})" class="flex-1 flex flex-col">
                        {{-- Chat header --}}
                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <div class="flex items-center space-x-3">
                                @if ($conversation->type === 'group')
                                    <div class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $conversation->name }}</div>
                                @else
                                    @php
                                        $friend = $conversation->users->firstWhere('id', '!=', auth()->id());
                                    @endphp
                                    <div class="flex items-center space-x-3">
                                        <img src="https://placehold.co/80x80" class="h-8 w-8 rounded-full" alt="{{ $friend->username }}">
                                        <div>
                                            <div class="text-base font-semibold text-gray-800 dark:text-gray-100">{{ $friend->username }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Online</div> {{-- TODO: dynamic status --}}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($conversation->type === 'group')
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v.01M12 12v.01M12 18v.01" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded shadow-lg z-10">
                                        <ul class="text-sm text-gray-700 dark:text-gray-200">
                                            <li>
                                                <button @click="$dispatch('open-modal', 'manage-users')" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">Add/Remove Users</button>
                                            </li>
                                            <li>
                                                <button @click="$dispatch('open-modal', 'view-members')" class="w-full text-left px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">View Members</button>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('messages.leave', $conversation->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-red-400">Leave Chat</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- MESSAGES --}}
                        <div class="flex-1 p-4 overflow-y-auto bg-gray-50 dark:bg-gray-700" id="messages" x-ref="scroller">
                            <template x-if="messages.length === 0">
                                <div class="flex items-center justify-center text-gray-400 dark:text-gray-500 mt-10">
                                    <i class="fas fa-comments text-3xl mr-2"></i>
                                    <span>No messages yet</span>
                                </div>
                            </template>

                            <template x-for="m in messages" :key="m.id">
                                <div class="mb-4" :class="{'flex justify-end': m.sender.id === me, 'flex': m.sender.id !== me}">
                                    
                                    <!-- Avatar -->
                                    <template x-if="m.sender.id !== me">
                                        <img :src="m.sender.avatar ?? 'https://placehold.co/100x100'" class="h-8 w-8 rounded-full mr-3">
                                    </template>

                                    <!-- Bubble -->
                                    <div class="max-w-md" :class="m.sender.id === me ? 'text-right' : ''">
                                        <div
                                            :class="m.sender.id === me
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100'"
                                            class="rounded-lg px-4 py-2 shadow break-words space-y-2 text-sm"
                                        >
                                            <template x-if="m.message_type === 'text'">
                                                <p x-text="m.body"></p>
                                            </template>

                                            <template x-if="m.message_type === 'image'">
                                                <img :src="'/storage/' + m.attachment"
                                                    class="rounded-lg max-h-64 object-cover border border-gray-300 dark:border-gray-600">
                                            </template>

                                            <template x-if="m.message_type === 'file'">
                                                <a :href="'/storage/' + m.attachment" target="_blank"
                                                    class="flex items-center space-x-2 text-blue-600 dark:text-blue-400 hover:underline">
                                                    <i class="fas fa-file-alt text-lg"></i>
                                                    <span x-text="m.attachment.split('/').pop()"></span>
                                                </a>
                                            </template>
                                        </div>
                                        <span class="text-xs text-gray-500" x-text="time(m.created_at)"></span>
                                    </div>

                                    <template x-if="m.sender.id === me">
                                        <img :src="m.sender.avatar ?? 'https://placehold.co/100x100'" class="h-8 w-8 rounded-full ml-3">
                                    </template>
                                </div>
                            </template>
                        </div>

                        {{-- INPUT --}}
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <form @submit.prevent="send">
                                <div class="flex flex-col gap-2">
                                    <!-- Attachment preview (if file selected) -->
                                    <template x-if="file">
                                        <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 p-2 rounded">
                                            <div class="flex items-center space-x-2 truncate">
                                                <i class="fas fa-paperclip text-gray-600 dark:text-gray-300"></i>
                                                <span class="text-sm text-gray-700 dark:text-gray-200 truncate" x-text="file.name"></span>
                                            </div>
                                            <button type="button" @click="file = null; $refs.file.value = ''" class="text-red-500 hover:text-red-700">
                                                <i class="fas fa-times-circle"></i>
                                            </button>
                                        </div>
                                    </template>

                                    <div class="flex items-center gap-2">
                                        <!-- File input -->
                                        <input type="file" x-ref="file" class="hidden" @change="handleFile">

                                        <button type="button" @click="$refs.file.click()" class="p-2 bg-gray-200 dark:bg-gray-600 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500">
                                            <i class="fas fa-paperclip text-gray-700 dark:text-gray-200"></i>
                                        </button>

                                        <!-- Message input -->
                                        <input x-model="newMessage" placeholder="Type a message..."
                                            class="flex-1 px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 border-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 text-sm text-gray-800 dark:text-gray-100">

                                        <!-- Send -->
                                        <button type="submit" class="p-2 rounded-full bg-blue-500 text-white hover:bg-blue-600">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                @else
                    <div class="flex flex-col items-center justify-center h-full text-center text-gray-600 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mb-4 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m-3-12a9 9 0 110 18 9 9 0 010-18z" />
                        </svg>
                        <h2 class="text-xl font-semibold mb-2">No Conversation Selected</h2>
                        <p class="mb-4">Start a new conversation by clicking the <strong>+</strong> icon above.</p>
                        <button
                            @click="$dispatch('open-modal', 'create-convo')"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Start New Chat
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <x-modal name="create-convo" maxWidth="md">
            <div class="p-6" x-data="createConversation()">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-100">Start a Conversation</h2>

                <input x-model="search" type="text" placeholder="Search users..."
                    class="w-full px-4 py-2 rounded border bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 mb-3">

                <div class="max-h-40 overflow-y-auto space-y-2">
                    <template x-for="user in filteredUsers" :key="user.id">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" :value="user.id" x-model="selected" class="accent-blue-600">
                            <span x-text="user.username" class="text-sm text-gray-800 dark:text-gray-200"></span>
                        </label>
                    </template>
                </div>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-show="selected.length === 1">
                    This will start a <strong>direct chat</strong>.
                </p>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" x-show="selected.length > 1">
                    This will create a <strong>group chat</strong> with <span x-text="selected.length"></span> members.
                    <label class="block text-sm text-gray-700 dark:text-gray-200 mb-1">Group name (optional)</label>
                    <input x-model="groupName"
                        type="text"
                        placeholder="Enter group name"
                        class="w-full px-4 py-2 rounded border bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                </p>
                <div class="flex justify-end mt-4 space-x-3">
                    <button type="button" @click="$dispatch('close')" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded">Cancel</button>
                    <button type="button" @click="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                </div>
            </div>
        </x-modal>

<script>
    function chat(conversationId, csrf, meId) {
        return {
            me: meId,
            messages: [],
            newMessage: '',
            file: null,

            init() {
                fetch(`/messages/${conversationId}/fetch`)
                    .then(r => r.json())
                    .then(data => {
                        this.messages = data;
                        this.$nextTick(() => this.scrollBottom());
                    });

                window.Echo.private(`conversation.${conversationId}`)
                    .listen('.message.sent', (e) => {
                        if (!this.messages.some(m => m.id === e.id)) {
                            this.messages.push(e);
                            this.$nextTick(() => this.scrollBottom());
                        }
                    });
            },

            handleFile(event) {
                this.file = event.target.files[0];
            },

            send() {
                if (!this.newMessage && !this.file) return;

                const formData = new FormData();
                if (this.newMessage) formData.append('body', this.newMessage);
                if (this.file) formData.append('attachment', this.file);

                fetch(`/messages/${conversationId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: formData,
                })
                .then(r => r.json())
                .then(msg => {
                    this.newMessage = '';
                    this.file = null;
                    this.$refs.file.value = ''; // reset input
                })
                .catch(err => console.error('[chat] SEND ERROR', err));
            },

            scrollBottom() {
                this.$refs.scroller.scrollTop = this.$refs.scroller.scrollHeight;
            },

            time(ts) {
                return new Date(ts).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }
        }
    }

    function createConversation() {
        return {
            search: '',
            selected: [],
            groupName: '',
            users: @json($allUsers),
            get filteredUsers() {
                return this.users.filter(u =>
                    (u.username ?? '').toLowerCase().includes(this.search.toLowerCase())
                );
            },
            submit() {
                if (this.selected.length === 0) {
                    console.warn('[Create Chat] No user selected.');
                    return;
                }

                if (this.selected.length === 1) {
                    // Direct chat
                    fetch('{{ route('messages.start-direct') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: this.selected[0] })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to create direct chat');
                        return response.json();
                    })
                    .then(data => {
                        window.location.href = data.url;
                    })
                    .catch(error => console.error('[Create Chat] Error (direct):', error));
                } else {
                    // if (this.groupName.trim() === '') {
                    //     alert('Please enter a group name.');
                    //     return;
                    // }
                    // Group chat
                    fetch('{{ route('messages.create-group') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            user_ids: this.selected,
                            name: this.groupName
                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Failed to create group chat');
                        return response.json();
                    })
                    .then(data => {
                        window.location.href = data.url;
                    })
                    .catch(error => console.error('[Create Chat] Error (group):', error));
                }
            }

        }
    }
    function chatSidebar() {
        return {
            search: '',
            groups: {!! json_encode(collect($groups)->map(function ($c) use ($conversation) {
                return [
                    'id' => $c->id,
                    'name' => $c->name ?? 'Group #' . $c->id,
                    'last' => optional($c->lastMessage)?->sender_id === auth()->id()
                        ? 'You: ' . optional($c->lastMessage)?->body
                        : optional($c->lastMessage)?->body,
                    'time' => optional($c->lastMessage)?->created_at?->diffForHumans(null, true),
                    'url' => route('messages.index', $c),
                    'active' => optional($conversation)->id === $c->id,
                ];
            })->values()) !!},

            directs: {!! json_encode(collect($directs)->map(function ($c) use ($conversation) {
                $friend = $c->users->firstWhere('id', '!=', auth()->id());
                return [
                    'id' => $c->id,
                    'name' => $friend->username,
                    'avatar' => 'https://placehold.co/80x80',
                    'last' => optional($c->lastMessage)?->sender_id === auth()->id()
                        ? 'You: ' . optional($c->lastMessage)?->body
                        : optional($c->lastMessage)?->body,
                    'time' => optional($c->lastMessage)?->created_at?->diffForHumans(null, true),
                    'url' => route('messages.index', $c),
                    'active' => optional($conversation)->id === $c->id,
                ];
            })->values()) !!},

            get filteredGroups() {
                return this.groups.filter(c =>
                    c.name.toLowerCase().includes(this.search.toLowerCase())
                );
            },
            get filteredDirects() {
                return this.directs.filter(c =>
                    c.name.toLowerCase().includes(this.search.toLowerCase())
                );
            }
        };
    }
</script>

</x-layouts.app>

