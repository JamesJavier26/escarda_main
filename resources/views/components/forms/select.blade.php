@props([
    'label'        => null,      // Text for the <label>
    'name',                       // Name / id of the <select>
    'options'      => [],        // ['value' => 'Label', ...] or [['value' => '', 'label' => '', 'disabled' => false], ...]
    'selected'     => null,      // Pre‑selected value
    'placeholder'  => null,      // Optional first “— Select —” option
    'error'        => false,     // Force error state (optional)
    'class'        => '',        // Extra classes for the <select>
    'labelClass'   => '',        // Extra classes for the <label>
])

{{-- LABEL --}}
@if ($label)
    <label
        for="{{ $name }}"
        {{ $attributes->merge(['class' => "block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 $labelClass"]) }}>
        {{ $label }}
    </label>
@endif

{{-- SELECT --}}
<select
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => "w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700
                    border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500
                    focus:border-transparent $class" .
                    ($error || $errors->has($name) ? ' border-red-500' : ''),
    ]) }}>

    {{-- Placeholder / empty option --}}
    @if ($placeholder !== null)
        <option value="" disabled {{ old($name, $selected) === null ? 'selected' : '' }}>
            {{ $placeholder }}
        </option>
    @endif

    {{-- Options (accepts   ['value' => 'Label']   OR   [['value'=>'','label'=>'','disabled'=>false], …] ) --}}
    @foreach ($options as $key => $option)
        @php
            // Normalize to array with keys: value, label, disabled
            $opt = is_array($option)
                ? array_merge(['value' => $option['value'] ?? $key, 'label' => $option['label'] ?? $option['value'], 'disabled' => $option['disabled'] ?? false], $option)
                : ['value' => $key, 'label' => $option, 'disabled' => false];
        @endphp

        <option
            value="{{ $opt['value'] }}"
            {{ old($name, $selected) == $opt['value'] ? 'selected' : '' }}
            {{ $opt['disabled'] ? 'disabled' : '' }}>
            {{ $opt['label'] }}
        </option>
    @endforeach
</select>

{{-- ERROR MESSAGE --}}
@if ($errors->has($name))
    <span class="text-red-500">{{ $errors->first($name) }}</span>
@endif
