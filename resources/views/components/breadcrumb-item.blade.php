{{-- resources/views/components/breadcrumb-item.blade.php --}}
@props([
    'href' => null,
    'active' => false,
    'icon' => null
])

<li class="inline-flex items-center">
    

    @if($href && !$active)
        <a href="{{ $href }}" class="text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center">
            @if($icon)
                @include("icons.{$icon}", ['class' => 'w-4 h-4 mr-1'])
            @endif
            {{ $slot }}
        </a>
    @else
        <span class="text-gray-500 dark:text-gray-400 inline-flex items-center">
            @if($icon)
                @include("icons.{$icon}", ['class' => 'w-4 h-4 mr-1'])
            @endif
            {{ $slot }}
        </span>
    @endif
</li>