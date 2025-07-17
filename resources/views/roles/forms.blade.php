@props(['roles'])

@php
    $editing = $role->exists;
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('roles.update', $role) : route('roles.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 space-y-6">
        {{-- Role Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Role Name <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $role->name ?? '') }}"
                placeholder="Enter role name"
                required
                @class([
                    'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                    border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                    focus:ring-blue-500 focus:border-transparent',
                    'border-red-500 dark:border-red-500' => $errors->has('name')
                ])>
            @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Permissions --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assign Permissions</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($permissions as $permission)
                    <label class="inline-flex items-center">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $permission->name }}"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:bg-gray-700 dark:text-white shadow-sm focus:ring-blue-500"
                            {{ isset($role) && $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-800 dark:text-gray-200">{{ $permission->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('permissions') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="pt-4">
        <button
            type="submit"
            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition duration-150 ease-in-out">
            {{ $editing ? 'Update Role' : 'Create Role' }}
        </button>
    </div>
</form>