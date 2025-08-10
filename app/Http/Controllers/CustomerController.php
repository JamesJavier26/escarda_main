<?php

namespace App\Http\Controllers;

use App\Models\BusinessType;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Customer::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('business_style', 'like', '%' . $search . '%')
                ->orWhere('business_type', 'like', '%' . $search . '%')
                ->orWhere('tin_no', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('contact_person', 'like', '%' . $search . '%');
            });
        }

        $customers = $query->paginate(10)->appends(['search' => $search]);

        return view('customers.index', compact('customers'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
        return view('customers.create', ['customer' => new Customer()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'telno' => 'required|string|max:20',
            'faxno' => 'nullable|string|max:20',
            'email' => 'required|email|unique:customers,email',
            'mobile_no' => 'nullable|string|max:20',
            'tin_no' => 'nullable|string|max:50|unique:customers,tin_no',
            'contact_person' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'terms_of_payment' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|max:50',
            'business_style' => 'nullable|string|max:100',
            'customer_category' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'tax_status' => 'required|string',
        ]);

        // Create the customer
        $customer = Customer::create($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
       return view('customers.edit', ['customer' => $customer]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'telno' => 'required|string|max:20',
            'faxno' => 'nullable|string|max:20',
            'email' => ['required', 'email', Rule::unique('customers')->ignore($customer->id)],
            'mobile_no' => 'nullable|string|max:20',
            'tin_no' => ['nullable', 'string', 'max:50', Rule::unique('customers')->ignore($customer->id)],
            'contact_person' => 'nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'terms_of_payment' => 'nullable|string|max:100',
            'business_type' => 'nullable|string|max:50',
            'business_style' => 'nullable|string|max:100',
            'customer_category' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
            'tax_status' => 'required|string',
        ]);

        // Update the customer
        $customer->update($validated);

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }

        public function getBusinessTypes()
        {
            return response()->json(BusinessType::pluck('name')->toArray());
        }

        public function storeBusinessType(Request $request)
        {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:business_types,name',
            ]);

            $type = BusinessType::create(['name' => $validated['name']]);
            return response()->json($type);
        }
}
