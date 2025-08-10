<x-layouts.app title="Employees">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Employees</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all registered employees</p>
    </div>



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

                        {{-- Department Filter --}}
                        <select name="department_id"
                            class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                            <option value="">All Departments</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Position Filter --}}
                        <select name="position_id"
                            class="px-3 py-2 rounded-lg text-sm bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-800 dark:text-gray-200 focus:outline-none">
                            <option value="">All Positions</option>
                            @foreach ($positions as $position)
                                <option value="{{ $position->id }}" {{ request('position_id') == $position->id ? 'selected' : '' }}>
                                    {{ $position->title }}
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
                        <a href="{{ route('employees.index') }}"
                            class="px-3 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                            <i class="fas fa-times-circle"></i>
                        </a>
                        @endif
                    </form>

                    {{-- Add New Button --}}
                    @hasrole('super-admin|admin')
                    <a href="{{ route('employees.create') }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Add new
                    </a>
                    @endhasrole
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Department</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Position</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                            @hasrole('super-admin')
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                            @endhasrole
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($employees as $employee)
                            <tr>
                                <td class="px-6 py-4 text-xs text-gray-800 dark:text-gray-200">
                                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-md dark:bg-blue-900 dark:text-blue-200">
                                        {{ $employee->employee_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $employee->prefix ? $employee->prefix . ' ' : '' }}
                                    {{ $employee->first_name }} {{ $employee->middle_name }} {{ $employee->last_name }} {{ $employee->suffix }}
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->address }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $employee->department->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $employee->position->title ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    {{ optional($employee->hire_date)->format('M d, Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    @if ($employee->user)
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $employee->user->username }}</span>
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->user->email }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">—</span>
                                    @endif
                                </td>
                                {{-- Status --}}
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                    @php $status = $employee->is_active ? 'active' : 'inactive'; @endphp
                                    <x-status-badge :status="$status">{{ ucfirst($status) }}</x-status-badge>
                                </td>

                                {{-- Actions --}}
                                @hasrole('super-admin')
                                <td class="px-6 py-4 text-sm text-right">
                                    <div class="flex justify-end items-center space-x-2">

                                        {{-- Edit --}}
                                        <a href="{{ route('employees.edit', $employee) }}"
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-green-600 hover:bg-green-700 text-white"
                                            title="Edit User">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>

                                        {{-- Delete --}}
                                        <div x-data>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" x-ref="deleteForm">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    @click="Swal.fire({
                                                        title: 'Delete this employee?',
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
                    {{ $employees->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
</x-layouts.app>
