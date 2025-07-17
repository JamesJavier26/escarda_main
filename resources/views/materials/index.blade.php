<x-layouts.app>
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Materials</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all materials</p>
    </div>


    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        {{-- Filters & Actions --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex flex-col-reverse gap-4 md:flex-row md:items-end md:justify-between">
                <form method="GET" class="flex flex-wrap items-end gap-3 w-full md:w-auto">
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


                    <button type="submit"
                            class="px-4 py-2 rounded-md bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium">
                            <i class="fas fa-filter"></i>
                    </button>
                    @if(request()->hasAny(['search']))
                        <a href="{{ route('materials.index') }}"
                            class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                            <i class="fas fa-times-circle"></i> 
                        </a>
                    @endif
                </form>


                <div class="flex justify-end">
                    <a href="{{ route('materials.create') }}"
                    class="inline-flex items-center h-[42px] px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm whitespace-nowrap">
                        <i class="fas fa-plus mr-1"></i>
                        Add New
                    </a>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>              
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Short Name</th>              
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>              
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($materials as $material)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <div class="font-semibold">{{ $material->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <div class="font-semibold">{{ $material->short_name ?? '—' }}</div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                @php
                                    $status = $material->is_active ? 'active' : 'inactive';
                                @endphp
                                <x-status-badge :status="$status">{{  ucfirst($status) }}</x-status-badge>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-sm text-right">
                                <div class="flex justify-end items-center space-x-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('materials.edit', $material) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-green-600 hover:bg-green-700 text-white"
                                        title="Edit User">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </a>
                                    @hasrole('super-admin')
                                    {{-- Delete --}}
                                    <div x-data>
                                        <form action="{{ route('materials.destroy', $material) }}" method="POST" x-ref="deleteForm">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                @click="Swal.fire({
                                                    title: 'Delete this material?',
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
                                    @endhasrole
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">No result found.</td>
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
                {{ $materials->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>  
</x-layouts.app>
