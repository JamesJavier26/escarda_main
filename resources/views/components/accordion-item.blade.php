{{-- resources/views/components/accordion-item.blade.php --}}
@props(['title', 'index'])

<div class="border border-gray-200 dark:border-gray-700 rounded-md overflow-hidden">
    <button
        @click="toggle({{ $index }})"
        class="flex justify-between items-center w-full px-4 py-3 text-left text-gray-800 dark:text-gray-200 font-medium bg-gray-50 dark:bg-gray-750 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        <span>{{ $title }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" :class="{ 'rotate-180': isActive({{ $index }}) }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="isActive({{ $index }})"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="px-4 py-3 text-gray-600 dark:text-gray-400"
    >
        {{ $slot }}
    </div>
</div>