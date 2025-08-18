<x-layouts.app title="Users">
  
    <div class="p-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            {{-- Filters & Actions --}}
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex flex-col-reverse gap-4 md:flex-row md:items-end md:justify-between">
                    <form method="GET" class="flex flex-wrap items-end gap-3 w-full md:w-auto">
                        {{-- Search --}}
                        <div class="relative">
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Search..."
                                class="pl-10 pr-4 py-2 w-full md:w-64 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                            <div class="absolute left-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Role Filter --}}
                        <select name="role"
                            class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                            <option value="">All Roles</option>
                            @foreach ($roleOptions as $role)
                                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Status Filter --}}
                        <select name="is_active"
                            class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                            <option value="">All Statuses</option>
                            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                        </select>

                        <button type="submit"
                            class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                            <i class="fas fa-filter"></i>
                        </button>

                        @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('users.index') }}"
                            class="px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        @endif
                    </form>

                    {{-- Add New Button --}}
                    @hasrole('admin|super-admin')
                    <a href="{{ route('users.create') }}"
                        class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Add User
                    </a>
                    @endhasrole
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Joined</th>
                            @hasrole('super-admin')
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($users as $user)
                            <tr>
                                {{-- User Info --}}
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    <div class="flex items-center">
                                        @if($user->avatar)
                                            <img src="{{ $user->avatar_url }}" class="h-10 w-10 rounded-full" alt="{{ $user->username }}">
                                        @else
                                            <span class="flex h-10 w-10 rounded-full items-center justify-center bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                                {{ $user->initials() }}
                                            </span>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->username }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $user->roles->pluck('name')->join(', ') }}
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    @php $status = $user->is_active ? 'active' : 'inactive'; @endphp
                                    <x-status-badge :status="$status">{{ ucfirst($status) }}</x-status-badge>
                                </td>

                                {{-- Joined Date --}}
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>

                                {{-- Actions --}}
                                @hasrole('super-admin')
                                <td class="px-6 py-4 text-sm text-right">
                                    <div class="flex justify-end items-center space-x-2">
                                        {{-- View --}}
                                        {{-- <a href="{{ route('users.show', $user) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-600 hover:bg-blue-700 text-white"
                                            title="View User">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a> --}}

                                        {{-- Edit --}}
                                        <a href="{{ route('users.edit', $user) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-green-600 hover:bg-green-700 text-white"
                                            title="Edit User">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <div x-data>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" x-ref="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    @click="Swal.fire({
                                                        title: 'Delete this user?',
                                                        text: 'This action cannot be undone.',
                                                        icon: 'warning',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#e3342f',
                                                        cancelButtonColor: '#6b7280',
                                                        confirmButtonText: 'Yes, delete it!',
                                                        reverseButtons: true
                                                    }).then((result) => {
                                                        if (result.isConfirmed) $refs.deleteForm.submit()
                                                    })"
                                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-red-600 hover:bg-red-700 text-white"
                                                    title="Delete User">
                                                    <i class="far fa-trash-alt text-xs"></i>
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </td>
                                @endhasrole

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div class="px-4 py-3 flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-sm">
                <div class="flex items-center gap-2">
                    <form method="GET" class="flex items-center gap-2">
                        <span class="text-gray-600 dark:text-gray-300">Show</span>
                        <select name="perPage" onchange="this.form.submit()"
                            class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none">
                            @foreach ([10, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                        <span class="text-gray-600 dark:text-gray-300">entries</span>
                        @foreach (request()->except(['perPage', 'page']) as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                    </form>
                </div>
                <div class="flex justify-center md:justify-end">
                    {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>