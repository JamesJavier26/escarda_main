<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index1()
    {
        $roles = Role::with('permissions')->paginate(10);
        return view('roles.index', compact('roles'));
    }
    public function index(Request $request)
    {
        // Validate perPage
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        // Base query
        $query = Role::with('permissions');

        // Search filter (by username or email)
        if ($search = $request->input('search')) {
            Log::info('Applying search filter', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('guard_name', 'like', "%{$search}%");
            });
        }

        // Fetch paginated results
        $roles = $query->latest('created_at')
            ->paginate($perPage)
            ->appends($request->except('page'));



        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('roles.create', [
            'role' => new Role(),
            'permissions'=> Permission::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array|nullable',
        ]);

        $role = Role::create(['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
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
    public function edit(Role $role)
    {
        return view('roles.edit', [
            'role' => $role,
            'permissions'=> Permission::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
    $data = $request->validate([
        'name' => 'required|unique:roles,name,' . $role->id,
        'permissions' => 'array|nullable',
    ]);

    $role->update(['name' => $data['name']]);
    $role->syncPermissions($data['permissions'] ?? []);

    return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Optional: Log or prevent deletion of critical roles like 'super-admin'
        if (in_array($role->name, ['super-admin'])) {
            return back()->with('error', 'This role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }

}
