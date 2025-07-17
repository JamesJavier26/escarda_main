<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(Request $request)
    {

        $query = Material::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                 ->orWhere('short_name', 'like', '%' . $search . '%');
            });
        }

        $materials = $query->paginate(10)->appends(['search' => $search]);

        return view('materials.index', compact('materials'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('materials.create', ['material' => new Material()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $material = Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material created successfully');
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
    public function edit(Material $material)
    {
        return view('materials.edit',['material' => $material]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $material = Material::create($validated);

        return redirect()->route('materials.index')
            ->with('success', 'Material updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        $material->delete();
          return redirect()->back()
            ->with('success', 'Material deleted successfully');
    }
}
