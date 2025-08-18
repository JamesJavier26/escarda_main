@props(['item', 'categories', 'units'])

@php
    $editing = $item->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('items.update', $item) : route('items.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            {{-- First Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Item Number --}}
                <div>
                    <label for="number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Item Number <span class="text-red-500">*</span>
                    </label>
                    <input id="number" name="number" type="text"
                        value="{{ old('number', $item->number) }}"
                        placeholder="Enter item number"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('number')
                        ])>
                    @error('number') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Barcode --}}
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Barcode
                    </label>
                    <input id="barcode" name="barcode" type="text"
                        value="{{ old('barcode', $item->barcode) }}"
                        placeholder="Enter barcode"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('barcode')
                        ])>
                    @error('barcode') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', $item->name) }}"
                        placeholder="Enter item name"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('name')
                        ])>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Category --}}
                <div x-data="categorySelector({{ json_encode(old('category_id', $item->category_id ?? '') ?? '') }})" x-init="fetchCategories()" class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Category
                        </label>
                        <button type="button" @click="showModal = true" class="text-xs text-blue-500 hover:underline">
                            + Add Category
                        </button>
                    </div>

                    <div x-show="error" class="text-sm text-red-500" x-text="error"></div>

                    <select 
                        id="category_id" 
                        name="category_id" 
                        x-model="selected"
                        x-bind:disabled="isLoading"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                        <option value="" disabled>Select category</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.id" x-text="cat.name" :selected="cat.id == selected"></option>
                        </template>
                    </select>

                    <!-- Modal -->
                    <div x-show="showModal" x-transition.opacity x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                        <div @click.outside="showModal = false" class="w-full max-w-md bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                            <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Add Category</h2>

                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Name <span class="text-red-500">*</span></label>
                                    <input 
                                        x-model="form.name" 
                                        type="text" 
                                        class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600"
                                        :class="{'border-red-500': validationError}"
                                    >
                                    <template x-if="validationError">
                                        <p class="text-xs text-red-500 mt-1" x-text="validationError"></p>
                                    </template>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Code</label>
                                    <input x-model="form.code" type="text" class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600">
                                </div>
                                <div>
                                    <label class="text-sm text-gray-700 dark:text-gray-300">Description</label>
                                    <textarea x-model="form.description" class="w-full mt-1 px-3 py-2 border rounded bg-white dark:bg-gray-700 dark:text-white border-gray-300 dark:border-gray-600"></textarea>
                                </div>
                            </div>

                            <div class="flex justify-end mt-4 space-x-3">
                                <button type="button" @click="showModal = false"
                                        class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white rounded-md">
                                    Cancel
                                </button>
                                <button type="button" @click="addNewCategory"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 flex items-center justify-center"
                                        :disabled="isSaving"
                                >
                                    <svg x-show="isSaving" class="w-4 h-4 mr-2 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    <span x-text="isSaving ? 'Saving...' : 'Save'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Description --}}
            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea id="description" name="description" rows="4"
                    placeholder="Item description"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                         border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                         focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('description')
                    ])>{{ old('description', $item->description) }}</textarea>
                @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Third Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Unit --}}
                <div>
                    <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Unit <span class="text-red-500">*</span>
                    </label>
                    <select id="unit_id" name="unit_id"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('unit_id')
                        ])>
                        <option value="" disabled selected>Select unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" @selected(old('unit_id', $item->unit_id) == $unit->id)>
                                {{ $unit->short_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('unit_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Is Taxable --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Taxable
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_taxable" value="1"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:ring-blue-500 dark:bg-gray-700"
                            @checked(old('is_taxable', $item->is_taxable))>
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Is taxable</span>
                    </label>
                </div>
            </div>

            {{-- Remarks --}}
            <div class="mt-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Remarks
                </label>
                <textarea id="remarks" name="remarks" rows="3"
                    placeholder="Optional remarks"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                         border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                         focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('remarks')
                    ])>{{ old('remarks', $item->remarks) }}</textarea>
                @error('remarks') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Status --}}
            <div class="mt-4">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</span>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="1"
                            @checked(old('is_active', (int) $item->is_active) === 1)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="is_active" value="0"
                            @checked(old('is_active', (int) $item->is_active) === 0)
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                    </label>
                </div>
                @error('is_active') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('items.index') }}"
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

<script>
function categorySelector(initialValue = '') {
    return {
        categories: [],
        selected: initialValue,
        showModal: false,
        isLoading: false,
        isSaving: false,
        error: null,
        validationError: null,
        form: {
            name: '',
            code: '',
            description: '',
        },

        async fetchCategories() {
            this.isLoading = true;
            this.error = null;
            try {
                const res = await fetch('/api/item-categories');
                if (!res.ok) throw new Error('Failed to fetch categories');
                const data = await res.json();
                this.categories = Array.isArray(data) ? data : [];

                if (this.selected && !this.categories.find(c => c.id == this.selected)) {
                    // Optional: fetch and add manually
                }
            } catch (err) {
                this.error = 'Failed to load categories.';
                console.error(err);
            } finally {
                this.isLoading = false;
            }
        },

        async addNewCategory() {
            this.validationError = null;

            if (!this.form.name.trim()) {
                this.validationError = 'Category name is required.';
                return;
            }

            this.isSaving = true;

            try {
                const res = await fetch('{{ route("item-categories.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(this.form)
                });

                if (!res.ok) throw new Error('Failed to create category');

                const newCategory = await res.json();
                this.categories.push(newCategory);
                this.categories.sort((a, b) => a.name.localeCompare(b.name));
                this.selected = newCategory.id;
                this.form = { name: '', code: '', description: '' };
                this.showModal = false;

                Swal.fire({
                    icon: 'success',
                    title: 'Added!',
                    text: 'Category has been added.',
                    timer: 1500,
                    showConfirmButton: false
                });

            } catch (err) {
                console.error(err);
                this.error = 'Error adding category.';
            } finally {
                this.isSaving = false;
            }
        }
    }
}
</script>
