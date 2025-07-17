@props(['mould'])

@php
    $editing = $mould->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('moulds.update', $mould) : route('moulds.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Mould No ----------------------------------------------------------- --}}
                <div>
                    <label for="no"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Mould No. <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="no"
                        name="no"
                        type="text"
                        value="{{ old('no', $editing ? $mould->no : $mould_no) }}"
                        placeholder="Enter mould number"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('no')
                        ])>
                    @error('no') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

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
                        value="{{ old('name', $mould->name) }}"
                        placeholder="Enter mould name"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('name')
                        ])>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
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
                            @checked(old('is_active', (int) $mould->is_active) === 1)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                        >
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            name="is_active"
                            value="0"
                            @checked(old('is_active', (int) $mould->is_active) === 0)
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
        <a href="{{ route('moulds.index') }}"
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
