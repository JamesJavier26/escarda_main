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

    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mb-4">
            <label for="name"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" x-model="formData.name"
                placeholder="Enter unit name"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{'border-red-500 dark:border-red-500': errors.name}">
            <p x-show="errors.name" x-text="errors.name"
                class="mt-1 text-sm text-red-500"></p>
                <!-- @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror -->
        </div>
        <div class="mb-4">
            <label for="name"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Short Name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="short_name" x-model="formData.name"
                placeholder="Enter unit short name"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{'border-red-500 dark:border-red-500': errors.short_name}">
            <p x-show="errors.short_name" x-text="errors.short_name"
                class="mt-1 text-sm text-red-500"></p>
                <!-- @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror -->
        </div>

    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mb-4">
            <label for="stock"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Decimal Places <span class="text-red-500">*</span>
            </label>
            <input type="number" id="decimal_places" x-model="formData.decimal_places" placeholder="0"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{'border-red-500 dark:border-red-500': errors.decimal_places}">
            <p x-show="errors.decimal_places" x-text="errors.decimal_places"
                class="mt-1 text-sm text-red-500"></p>
        </div>
        <div class="mb-4">
            <label for="type"
                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Type <span class="text-red-500">*</span>
            </label>
            <select id="type" x-model="formData.type"
                class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{'border-red-500 dark:border-red-500': errors.type}">
                <option value="">Select unit type</option>
                    <option value="weight">Weight</option>
                    <option value="volume">Volume</option>
                    <option value="length">length</option>
                    <option value="count">Count</option>
                <option value="toys">Toys & Games</option>
            </select>
            <p x-show="errors.category" x-text="errors.category"
                class="mt-1 text-sm text-red-500"></p>
        </div>
    </div>    

    <div class="mb-4">
        <label for="description"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Description <span class="text-red-500">*</span>
        </label>
        <textarea id="description" x-model="formData.description" rows="4"
            placeholder="Enter product description"
            class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            :class="{'border-red-500 dark:border-red-500': errors.description}"></textarea>
        <p x-show="errors.description" x-text="errors.description"
            class="mt-1 text-sm text-red-500"></p>
    </div>

    <div class="mb-4">
        <label
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
        <div class="flex space-x-4">
            <div class="flex items-center">
                <input type="radio" id="status-active" name="status" value="1"
                    x-model="formData.status"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label for="status-active"
                    class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Active
                </label>
            </div>
            <div class="flex items-center">
                <input type="radio" id="status-inactive" name="status"
                    value="0" x-model="formData.status"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                <label for="status-inactive"
                    class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                    Inactive
                </label>
            </div>
        </div>
    </div>

    <div class="flex justify-end space-x-4">
        <a href="{{ route('units.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            Cancel
        </a>
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                {{ $editing ? 'Update' : 'Save' }}
        </button>
    </div>
</form>
