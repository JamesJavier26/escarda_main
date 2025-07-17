<x-layouts.app>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Website Settings</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">Configure site-wide options</p>
    </div>
    <form method="POST" action="{{ route('settings.update') }}" class="space-y-4">
        @csrf

        <div>
            {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Site Name</label> --}}
            <x-forms.input label="Site Name" name="site_name"  value="{{ $settings['site_name'] ?? '' }}" type="text" />
        </div>

        <div>
            {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Email</label> --}}
            {{-- <input name="contact_email" value="{{ $settings['contact_email'] ?? '' }}" class="w-full px-3 py-2 border rounded dark:bg-gray-800 dark:text-white"> --}}
            <x-forms.input label="Contact Email" name="contact_email"  value="{{ $settings['contact_email'] ?? '' }}" type="text" />
        </div>

        <div>
            {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Telephone Number</label> --}}
            {{-- <input name="telno" value="{{ $settings['telno'] ?? '' }}" class="w-full px-3 py-2 border rounded dark:bg-gray-800 dark:text-white"> --}}
            <x-forms.input label="Telephone Number" name="telno"  value="{{ $settings['telno'] ?? '' }}" type="text" />
        </div>

        <div>
            {{-- <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label> --}}
            {{-- <input name="phone" value="{{ $settings['phone'] ?? '' }}" class="w-full px-3 py-2 border rounded dark:bg-gray-800 dark:text-white"> --}}
            <x-forms.input label="Phone Number" name="phone"  value="{{ $settings['phone'] ?? '' }}" type="text" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
            <textarea name="address" rows="5" class="w-full px-3 py-2 border rounded dark:bg-gray-800 dark:text-white">{{ $settings['address'] ?? '' }}</textarea>
        </div>

        <div>
            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save Settings</button>
        </div>
    </form>
</x-layouts.app>
