<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnitRequest;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $query = Unit::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                 ->orWhere('singular_name', 'like', '%' . $search . '%')
                 ->orWhere('short_name', 'like', '%' . $search . '%');
            });
        }

        $units = $query->paginate(10)->appends(['search' => $search]);

        return view('units.index', compact('units'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('units.create', ['unit' => new Unit]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnitRequest $request)
    {
        Unit::create($request->validated());
        return to_route('units.index')->with('success', 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        Log::info('Unit Name:'. $unit->name);
        return view('units.edit', compact('unit'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(UnitRequest $request, Unit $unit)
    {
        $unit->update($request->validated());
        return to_route('units.index')->with('success', 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return back()->with('success', 'Unit deleted.');
    }
    
}
