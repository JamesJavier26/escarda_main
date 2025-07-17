<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ isset($permission) ? 'Edit' : 'Create' }} Permission</h1>
    </div>

    <form method="POST" action="{{ isset($permission) ? route('permissions.update', $permission) : route('permissions.store') }}">
        @csrf
        @if(isset($permission))
            @method('PUT')
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permission Name</label>
            <input type="text" name="name" value="{{ old('name', $permission->name ?? '') }}"
                class="w-full px-3 py-2 border rounded dark:bg-gray-800 dark:text-white" required>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            {{ isset($permission) ? 'Update' : 'Create' }}
        </button>
    </form>
</x-layouts.app>
