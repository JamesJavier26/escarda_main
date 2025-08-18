@props(['customer'])

@php
    $editing = $customer->exists;
    // $businessTypes = \App\Models\BusinessType::pluck('name')->toArray();
@endphp

<form
    {{ $attributes->merge(['class' => 'space-y-6']) }}
    method="POST"
    action="{{ $editing ? route('customers.update', $customer) : route('customers.store') }}"
>
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid grid-cols-1 gap-6 w-full">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full">
            {{-- First Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Customer Name <span class="text-red-500">*</span>
                    </label>
                    <input id="name" name="name" type="text"
                        value="{{ old('name', $customer->name) }}"
                        placeholder="Enter customer name"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('name')
                        ])>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- TIN No --}}
                <div>
                    <label for="tin_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        TIN No. 
                    </label>
                    <input id="tin_no" name="tin_no" type="text"
                        value="{{ old('tin_no', $customer->tin_no) }}"
                        placeholder="Enter TIN number"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('tin_no')
                        ])>
                    @error('tin_no') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Second Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email"
                        value="{{ old('email', $customer->email) }}"
                        placeholder="Enter email address"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('email')
                        ])>
                    @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Website --}}
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Website
                    </label>
                    <input id="website" name="website" type="url"
                        value="{{ old('website', $customer->website) }}"
                        placeholder="https://example.com"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('website')
                        ])>
                    @error('website') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Third Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Telephone --}}
                <div>
                    <label for="telno" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Telephone No. <span class="text-red-500">*</span>
                    </label>
                    <input id="telno" name="telno" type="text"
                        value="{{ old('telno', $customer->telno) }}"
                        placeholder="Enter telephone number"
                        @class([
                            'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                             border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                             focus:ring-blue-500 focus:border-transparent',
                            'border-red-500 dark:border-red-500' => $errors->has('telno')
                        ])>
                    @error('telno') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Fax No --}}
                <div>
                    <label for="faxno" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fax No.
                    </label>
                    <input id="faxno" name="faxno" type="text"
                        value="{{ old('faxno', $customer->faxno) }}"
                        placeholder="Enter fax number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Fourth Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Mobile No --}}
                <div>
                    <label for="mobile_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Mobile No.
                    </label>
                    <input id="mobile_no" name="mobile_no" type="text"
                        value="{{ old('mobile_no', $customer->mobile_no) }}"
                        placeholder="Enter mobile number"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Contact Person --}}
                <div>
                    <label for="contact_person" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Contact Person
                    </label>
                    <input id="contact_person" name="contact_person" type="text"
                        value="{{ old('contact_person', $customer->contact_person) }}"
                        placeholder="Enter contact person"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            {{-- Address --}}
            <div class="mt-4">
                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="3"
                    placeholder="Enter full address"
                    @class([
                        'w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                         border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                         focus:ring-blue-500 focus:border-transparent',
                        'border-red-500 dark:border-red-500' => $errors->has('address')
                    ])>{{ old('address', $customer->address) }}</textarea>
                @error('address') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Fifth Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            {{-- Terms of Payment --}}
            <div>
                <label for="terms_of_payment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Terms of Payment
                </label>
                <select id="terms_of_payment" name="terms_of_payment"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                        border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                        focus:ring-blue-500 focus:border-transparent">
                    <option value="30 days" {{ old('terms_of_payment', $customer->terms_of_payment) == '30 days' ? 'selected' : '' }}>30 days</option>
                    <option value="60 days" {{ old('terms_of_payment', $customer->terms_of_payment) == '60 days' ? 'selected' : '' }}>60 days</option>
                    <option value="90 days" {{ old('terms_of_payment', $customer->terms_of_payment) == '90 days' ? 'selected' : '' }}>90 days</option>
                    <option value="COD" {{ old('terms_of_payment', $customer->terms_of_payment) == 'COD' ? 'selected' : '' }}>COD</option>
                </select>
            </div>

            {{-- Business Type --}}
            <div class="space-y-2">
                <!-- Label -->
                <div class="flex items-center justify-between">
                    <label for="business_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Business Type
                    </label>
                </div>

                <!-- Error State -->
                @error('business_type')
                    <div class="text-sm text-red-500">{{ $message }}</div>
                @enderror

                <!-- Open Text Input -->
                <input 
                    type="text" 
                    id="business_type" 
                    name="business_type" 
                    value="{{ old('business_type', $customer->business_type ?? '') }}"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 focus:ring-blue-500"
                    placeholder="Enter business type"
                >
            </div>
        </div>

            {{-- Sixth Row --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                {{-- Business Style --}}
                <div>
                    <label for="business_style" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Business Style
                    </label>
                    <input id="business_style" name="business_style" type="text"
                        value="{{ old('business_style', $customer->business_style) }}"
                        placeholder="Enter business style"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                               border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                               focus:ring-blue-500 focus:border-transparent">
                </div>

                {{-- Customer Category --}}
                <div>
                    <label for="customer_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Customer Category
                    </label>
                    <input 
                        type="text" 
                        id="customer_category" 
                        name="customer_category" 
                        value="{{ old('customer_category', $customer->customer_category ?? '') }}"
                        class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                            border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                            focus:ring-blue-500 focus:border-transparent"
                        placeholder="Enter customer category"
                    >
                </div>
            </div>

            {{-- Tax Status --}}
            <div class="mt-4">
                <label for="tax_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tax Status
                </label>
                <select id="tax_status" name="tax_status"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                        border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                        focus:ring-blue-500 focus:border-transparent">
                    <option value="Zero Rated" {{ old('tax_status', $customer->tax_status) == 'Zero Rated' ? 'selected' : '' }}>Zero Rated</option>
                    <option value="VAT Inclusive" {{ old('tax_status', $customer->tax_status) == 'VAT Inclusive' ? 'selected' : '' }}>VAT Inclusive</option>
                </select>
            </div>

            {{-- Business Address --}}

            {{-- Remarks --}}
            <div class="mt-4">
                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Remarks
                </label>
                <textarea id="remarks" name="remarks" rows="3"
                    placeholder="Enter any remarks"
                    class="w-full px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300
                           border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2
                           focus:ring-blue-500 focus:border-transparent">{{ old('remarks', $customer->remarks) }}</textarea>
            </div>

            {{-- Status --}}
            <div class="mt-4">
                <span class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</span>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="active"
                            @checked(old('status', $customer->status) === 'active')
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="status" value="inactive"
                            @checked(old('status', $customer->status) === 'inactive')
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    {{-- Buttons --}}
    <div class="flex justify-end space-x-4">
        <a href="{{ route('customers.index') }}"
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
function businessTypeSelector(initialValue = '') {
    return {
        types: [],
        selected: initialValue,
        showModal: false,
        newType: '',
        isLoading: false,
        error: null,

        async fetchTypes() {
            this.isLoading = true;
            this.error = null;
            
            try {
                const res = await fetch('/api/business-types');
                if (!res.ok) throw new Error('Failed to fetch types');
                
                const data = await res.json();
                
                // Ensure we have an array
                this.types = Array.isArray(data) ? data : [];
                
                // If initial value is not empty and not in types, add it
                if (this.selected && !this.types.includes(this.selected)) {
                    this.types.push(this.selected);
                    // Sort alphabetically if desired
                    this.types.sort();
                }
            } catch (err) {
                console.error('Failed to load types:', err);
                this.error = 'Failed to load business types. Please try again.';
            } finally {
                this.isLoading = false;
            }
        },

        async addNewType() {
            if (!this.newType.trim()) return;

            try {
                const res = await fetch('/api/business-types', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    },
                    body: JSON.stringify({ name: this.newType.trim() })
                });

                if (!res.ok) throw new Error('Failed to add type');

                const data = await res.json();
                if (!this.types.includes(data.name)) {
                    this.types.push(data.name);
                    this.types.sort(); // Optional sorting
                }
                this.selected = data.name;
                this.newType = '';
                this.showModal = false;
            } catch (err) {
                console.error('Error adding type:', err);
                this.error = 'Failed to add business type. It may already exist.';
            }
        }
    };
}
</script>
