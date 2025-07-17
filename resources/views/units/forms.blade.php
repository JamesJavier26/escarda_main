@props(['unit'])

@php
    $editing = $unit->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('units.update', $unit) : route('units.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

         <div class="grid grid-cols-1 gap-6 w-full">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            value="{{ old('name', $unit->name) }}"
                            placeholder="Enter unit name"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('name')
                            ])>
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Singular Name -------------------------------------------------- --}}
                    <div>
                        <label for="singular_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Singular Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="singular_name"
                            name="singular_name"
                            type="text"
                            value="{{ old('singular_name', $unit->singular_name) }}"
                            placeholder="Enter unit singular name"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('singular_name')
                            ])>
                        @error('singular_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Short Name ----------------------------------------------------- --}}
                    <div>
                        <label for="short_name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Short Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="short_name"
                            name="short_name"
                            type="text"
                            value="{{ old('short_name', $unit->short_name) }}"
                            placeholder="Enter unit short name"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('short_name')
                            ])>
                        @error('short_name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Decimal Places -------------------------------------------------- --}}
                    <div>
                        <label for="decimal_places"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Decimal Places <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="decimal_places"
                            name="decimal_places"
                            type="number"
                            min="0" max="6"
                            value="{{ old('decimal_places', $unit->decimal_places ?? 2) }}"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('decimal_places')
                            ])>
                        @error('decimal_places') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Type ------------------------------------------------------------ --}}
                    <div>
                        <label for="type"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>

                        <select
                            id="type"
                            name="type"
                            @class([
                                'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                                border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                                focus:ring-blue-500 focus:border-transparent',
                                'border-red-500 dark:border-red-500' => $errors->has('type')
                            ])>

                            {{-- Placeholder --}}
                            <option value="" disabled
                                @selected(! old('type', $unit->type))>
                                Select unit type
                            </option>

                            {{-- Options --}}
                            @foreach(['weight'=>'Weight','volume'=>'Volume','length'=>'Length','count'=>'Count'] as $val => $label)
                                <option value="{{ $val }}"
                                    @selected(strtolower(old('type', $unit->type)) === $val)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('type') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Description -------------------------------------------------------- --}}
                <div>
                    <label for="description"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        placeholder="Enter unit description"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('description')
                        ])>{{ old('description', $unit->description) }}</textarea>
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status ------------------------------------------------------------- --}}
                <div>
                    <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</span>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                id="status-active"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', (int) $unit->is_active) === 1)
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input
                                type="radio"
                                id="status-inactive"
                                name="is_active"
                                value="0"
                                @checked(old('is_active', (int) $unit->is_active) === 0)
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                        </label>
                    </div>
                    @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
         </div>





    {{-- Buttons ------------------------------------------------------------ --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('units.index') }}"
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
