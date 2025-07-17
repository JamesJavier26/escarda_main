<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Validate perPage
        $perPage = in_array($request->input('perPage'), [10, 25, 50, 100])
            ? (int) $request->input('perPage')
            : 10;

        // Base query
        $query = User::with('roles');

        // Search filter (by username or email)
        if ($search = $request->input('search')) {
            Log::info('Applying search filter', ['search' => $search]);
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (strlen($request->input('role')) > 0) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        if (strlen($request->input('is_active')) > 0) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }


        // Clone for logging raw SQL & bindings
        $clone = clone $query;

        // Fetch paginated results
        $users = $query->latest('created_at')
            ->paginate($perPage)
            ->appends($request->except('page'));

        // Log final data
        Log::info('Final request params', $request->all());
        Log::info('Raw SQL query', ['sql' => $clone->toSql()]);
        Log::info('SQL bindings', ['bindings' => $clone->getBindings()]);
        Log::info('User result count', ['count' => $users->total()]);

        // Role options for filters
        $roleOptions = Role::pluck('name');

        return view('users.index', compact('users', 'roleOptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('users.create', [
            'user' => new User(),
            'roles' => Role::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'roles' => 'nullable|array',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.index')->with('success', 'User created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => Role::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required',
            'email' => "required|email|unique:users,email,{$user->id}",
            'password' => 'nullable|confirmed',
            'roles' => 'nullable|array',
        ]);

        $user->update([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        $user->syncRoles($request->roles ?? []);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            // Remove roles and permissions
            $user->roles()->detach();
            $user->permissions()->detach();

            // Delete user
            $user->delete();
        });

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
