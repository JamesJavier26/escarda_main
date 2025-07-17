@props(['currency'])

@php
    $editing = $currency->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('currencies.update', $currency) : route('currencies.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Name ----------------------------------------------------------- --}}
                <div>
                    <label for="name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $currency->name) }}"
                        placeholder="Enter currency name (e.g. US Dollar)"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('name')
                        ])>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Short Name ----------------------------------------------------- --}}
                <div>
                    <label for="short_name"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Short Name
                    </label>
                    <input
                        id="short_name"
                        name="short_name"
                        type="text"
                        value="{{ old('short_name', $currency->short_name) }}"
                        placeholder="Enter short name (e.g. USD)"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('short_name')
                        ])>
                    @error('short_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Symbol ----------------------------------------------------------- --}}
                <div>
                    <label for="symbol"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Symbol <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="symbol"
                        name="symbol"
                        type="text"
                        value="{{ old('symbol', $currency->symbol) }}"
                        placeholder="Enter symbol (e.g. $)"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('symbol')
                        ])>
                    @error('symbol') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description -------------------------------------------------------- --}}
            <div class="mt-4">
                <label for="description"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Description
                </label>
                <textarea
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Optional description"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                        border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                        focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('description')
                    ])>{{ old('description', $currency->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status ------------------------------------------------------------- --}}
            <div class="mt-4">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</span>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="is_active"
                            value="1"
                            @checked(old('is_active', (int) $currency->is_active) === 1)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="is_active"
                            value="0"
                            @checked(old('is_active', (int) $currency->is_active) === 0)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                    </label>
                </div>
                @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Buttons ------------------------------------------------------------ --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('currencies.index') }}"
           class="px-4 py-2 rounded-md shadow-sm text-sm font-medium
                  text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700
                  border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600
                  focus:outline-none focus:ring-2 focus:ring-blue-500">
            Cancel
        </a>
        <button type="submit"
                class="px-4 py-2 rounded-md shadow-sm text-sm font-medium text-white
                       bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{ $editing ? 'Update' : 'Save' }}
        </button>
    </div>
</form>
