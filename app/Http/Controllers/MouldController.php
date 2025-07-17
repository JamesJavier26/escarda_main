<?php

namespace App\Http\Controllers;

use App\Models\Mould;
use Illuminate\Http\Request;

class MouldController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $query = Mould::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $moulds = $query->paginate(10)->appends(['search' => $search]);

        return view('moulds.index', compact('moulds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('moulds.create', [
            'mould' => new Mould(),
            'mould_no' => (int) Mould::max('no') + 1,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'no' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Create the item with the current user as creator
        $mould = Mould::create($validated);

        return redirect()->route('moulds.index')
            ->with('success', 'Mould created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Mould $mould)
    {
         return view('moulds.edit', ['mould' => $mould]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Mould $mould)
    {
        $validated = $request->validate([
            'no' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        // Create the item with the current user as creator
        $mould->update($validated);

        return redirect()->route('moulds.index')
            ->with('success', 'Mould updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Mould $mould)
    {
        $mould->delete();
          return redirect()->back()
            ->with('success', 'Mould deleted successfully');
    }
}
