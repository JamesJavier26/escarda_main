<x-layouts.app title="Activity Log">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Activity Logs</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">List of all activities across the site</p>
    </div>

    {{-- Table Container --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        {{-- Filters --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
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

                @if(request()->has('search'))
                    <a href="{{ route('activities.index') }}"
                        class="px-4 py-2 rounded-md bg-red-600 hover:bg-red-700 text-white text-sm font-medium">
                        <i class="fas fa-times-circle"></i>
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Description</th>              
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Causer</th>              
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>                
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                @class([
                                    'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => str()->contains($activity->description, 'Created'),
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' => str()->contains($activity->description, 'Updated'),
                                    'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => str()->contains($activity->description, 'Deleted'),
                                    'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' => true, // fallback
                                ])">
                                {{ $activity->description }}
                            </span>

                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200">
                                {{ $activity->causer?->username ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                <div>{{ $activity->created_at->diffForHumans() }}</div>
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ $activity->created_at->format('F j, Y h:i A') }}
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-center align-middle">
                                @php
                                    $activityData = [
                                        'id' => $activity->id,
                                        'description' => $activity->description,
                                        'causer' => $activity->causer?->username ?? '—',
                                        'changes' => $activity->properties->toArray(),
                                        'date' => $activity->created_at->toDayDateTimeString(),
                                    ];
                                @endphp

                                <div class="flex justify-center items-center">
                                    <button
                                        @click="window.dispatchEvent(new CustomEvent('show-activity', { detail: @js($activityData) }))"
                                        class="w-8 h-8 flex items-center justify-center rounded-full bg-indigo-600 hover:bg-indigo-700 text-white"
                                        title="View Activity"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-sm text-center text-gray-600 dark:text-gray-300">
                                No results found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-4 py-3 flex justify-between items-center bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 text-sm">
            <div>
                <form method="GET" class="flex items-center gap-2">
                    <span class="text-gray-600 dark:text-gray-300">Show</span>
                    <select name="perPage" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 focus:outline-none">
                        @foreach ([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('perPage', 10) == $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span class="text-gray-600 dark:text-gray-300">entries</span>
                    @foreach (request()->except(['perPage', 'page']) as $name => $value)
                        <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
            <div>
                {{ $activities->appends(request()->query())->links('vendor.pagination.tailwind') }}
            </div>
        </div>
    </div>

    {{-- Global Modal --}}
    <x-modal name="activity-details" maxWidth="2xl">
        <div
            class="p-6 max-h-[80vh] overflow-y-auto"
            x-data="{
                open: false,
                activity: {},
                init() {
                    window.addEventListener('show-activity', e => {
                        this.activity = e.detail;
                        this.open = true;
                        this.$dispatch('open-modal', 'activity-details');
                    });
                }
            }"
            x-init="init()"
        >
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Activity Details</h2>

            <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                <p><strong>Description:</strong> <span x-text="activity.description"></span></p>
                <p><strong>Causer:</strong> <span x-text="activity.causer || '—'"></span></p>
                <p><strong>Date:</strong> <span x-text="activity.date"></span></p>

                <template x-if="activity.changes && Object.keys(activity.changes).length > 0">
                    <div>
                        <strong>Properties:</strong>
                        <pre class="mt-1 bg-gray-100 dark:bg-gray-900 text-xs rounded p-3 overflow-auto max-h-64"
                              x-text="JSON.stringify(activity.changes, null, 2)">
                        </pre>
                    </div>
                </template>
            </div>

            <div class="flex justify-end mt-6">
                <button @click="$dispatch('close')" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded text-sm">
                    Close
                </button>
            </div>
        </div>
    </x-modal>
</x-layouts.app>
