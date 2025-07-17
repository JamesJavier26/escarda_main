<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $query = Currency::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('short_name', 'like', '%' . $search . '%');
            });
        }

        $currencies = $query->paginate(10)->appends(['search' => $search]);

        return view('currencies.index', compact('currencies'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('currencies.create', ['currency' => new Currency()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string',
            'symbol' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Create the item with the current user as creator
        $currencies = Currency::create($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Currency created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Currency $currency)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        return view('currencies.edit', ['currency' => $currency]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string',
            'symbol' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $currency->update($validated);

        return redirect()->route('currencies.index')
            ->with('success', 'Currency updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
          return redirect()->back()
            ->with('success', 'Currency deleted successfully');
    }
}
