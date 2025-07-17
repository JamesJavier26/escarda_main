@props([
    'variant' => 'default',
    'size' => 'default',
    'pill' => false,
    'dot' => false
])

@php
$baseClasses = 'inline-flex items-center font-medium';

$variantClasses = [
    'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    'primary' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    'info' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
];

$sizeClasses = [
    'sm' => 'px-2 py-0.5 text-xs',
    'default' => 'px-2.5 py-0.5 text-xs',
    'lg' => 'px-3 py-0.5 text-sm',
];

$shapeClasses = $pill ? 'rounded-full' : 'rounded';

$classes = $baseClasses . ' ' . $variantClasses[$variant] . ' ' . $sizeClasses[$size] . ' ' . $shapeClasses;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <svg class="-ml-0.5 mr-1.5 h-2 w-2" fill="currentColor" viewBox="0 0 8 8">
            <circle cx="4" cy="4" r="3" />
        </svg>
    @endif
    {{ $slot }}
</span>