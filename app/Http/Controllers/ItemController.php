<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = Item::with(['category', 'unit', 'user']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('barcode', 'like', '%' . $search . '%');
            });
        }

        $items = $query->paginate(10)->appends(['search' => $search]);

        return view('items.index', compact('items'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('items.create',[
            'item' => new Item(),
            'categories' => ItemCategory::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'number' => 'required|string|max:50|unique:items,number',
            'barcode' => 'nullable|string|max:50|unique:items,barcode',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'description' => 'required|string',
            'unit_id' => 'required|exists:units,id',
            'is_taxable' => 'boolean',
            'remarks' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Create the item with the current user as creator
        $item = Item::create($validated + [
            'created_by' => auth()->id()
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => ItemCategory::orderBy('name')->get(),
            'units' => Unit::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('items')->ignore($item->id)
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('items')->ignore($item->id)
            ],
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:item_categories,id',
            'description' => 'required|string',
            'unit_id' => 'required|exists:units,id',
            'is_taxable' => 'boolean',
            'remarks' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Update the item with the current user as updater
        $item->update($validated + [
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('items.index')
            ->with('success', 'Item updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        //
    }
    public function search(Request $request)
    {
        $term = $request->input('q');
        $field = $request->input('field', 'all');
        $page = $request->input('page', 1);
        $perPage = 5;

        $items = Item::query()
            ->when($term, function ($q) use ($term, $field) {
                $q->where(function ($query) use ($term, $field) {
                    $likeTerm = '%' . $term . '%';

                    if ($field === 'number') {
                        $query->whereRaw('CAST(number AS CHAR) LIKE ?', [$likeTerm]);
                    } elseif ($field === 'name') {
                        $query->where('name', 'like', $likeTerm);
                    } elseif ($field === 'description') {
                        $query->where('description', 'like', $likeTerm);
                    } else {
                        $query->whereRaw('CAST(number AS CHAR) LIKE ?', [$likeTerm])
                            ->orWhere('name', 'like', $likeTerm)
                            ->orWhere('description', 'like', $likeTerm);
                    }
                });
            })
            ->orderByRaw('CAST(number AS UNSIGNED)')
            ->paginate($perPage, ['id', 'number', 'name', 'description','unit_id'], 'page', $page);

        return response()->json([
            'data' => $items->items(),
            'current_page' => $items->currentPage(),
            'last_page' => $items->lastPage(),
            'total' => $items->total(),
        ]);
    }



    public function quickAdd(Request $request)
    {
        $data = $request->validate([
            'category_id' => ['nullable','exists:item_categories,id'],
            'barcode'     => ['nullable','unique:items'],
            'name'        => ['required','string','max:255'],
            'description' => ['required','string'],
            'unit_id'     => ['required','exists:units,id'],
            'remarks'     => ['nullable','string'],
            'is_taxable'  => ['boolean'],
        ]);

        // sequential item number (e.g. 000123)
        $nextNo = str_pad((Item::max('id') ?? 0) + 1, 6, '0', STR_PAD_LEFT);

        $item = Item::create($data + [
            'number'      => $nextNo,
            'is_active'   => true,
            'created_by'  => auth()->id(),
        ]);

        return response()->json([
            'id'          => $item->id,
            'number'      => $item->number,
            'name'        => $item->name,
            'description' => $item->description,
        ]);
    }
    public function store_category(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $category = ItemCategory::create($validated);

        return response()->json($category);
    }

}
