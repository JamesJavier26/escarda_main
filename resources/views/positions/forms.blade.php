@props(['position'])

@php
    $editing = $position->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('positions.update', $position) : route('positions.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            {{-- First Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Position Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Position Title <span class="text-red-500">*</span>
                    </label>
                    <input id="title" name="title" type="text"
                        value="{{ old('title', $position->title) }}"
                        placeholder="Enter position title"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('title')
                        ])>
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Position Code --}}
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Position Code
                    </label>
                    <input id="code" name="code" type="text"
                        value="{{ old('code', $position->code) }}"
                        placeholder="Enter position code (e.g., POS-001)"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('code')
                        ])>
                    @error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Salary Range --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Minimum Salary --}}
                <div>
                    <label for="salary_min" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Minimum Salary
                    </label>
                    <input id="salary_min" name="salary_min" type="number" step="0.01"
                        value="{{ old('salary_min', $position->salary_min) }}"
                        placeholder="Enter minimum salary"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('salary_min')
                        ])>
                    @error('salary_min') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Maximum Salary --}}
                <div>
                    <label for="salary_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Maximum Salary
                    </label>
                    <input id="salary_max" name="salary_max" type="number" step="0.01"
                        value="{{ old('salary_max', $position->salary_max) }}"
                        placeholder="Enter maximum salary"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('salary_max')
                        ])>
                    @error('salary_max') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Position description"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                         border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                         focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('description')
                    ])>{{ old('description', $position->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Checkboxes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Managerial Position --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Position Type
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_managerial" value="1"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700"
                            @checked(old('is_managerial', $position->is_managerial ?? false))>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Managerial Position</span>
                    </label>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Status
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_active" value="1"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700"
                            @checked(old('is_active', $position->is_active ?? true))>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Position</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('positions.index') }}"
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