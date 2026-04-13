<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{
    // Show the list of users
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $users = User::query()
            ->with(['branch', 'branches'])
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->sort, fn($q) => $q->sort(
                $request->sort,
                $request->input('direction', 'asc')
            ))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    // Show the form for creating a new user
    public function create()
    {
        $branches = Branch::all(); // Fetch all branches to populate the dropdown
        $roleOptions = User::distinctRolesFromDatabase();

        return inertia('Admin/User/Create', compact('branches', 'roleOptions'));
    }

    // Store a newly created user in storage
    public function store(Request $request)
    {
        $allowedRoles = User::distinctRolesFromDatabase();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'role' => ['required', 'string', 'max:255', Rule::in($allowedRoles)],
            'password' => 'required|string|min:8|confirmed',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'branch_id' => $request->branch_id,
        ]);

        // Attach the selected branches to the user
        if ($request->branch_ids) {
            $user->branches()->sync($request->branch_ids); // Sync with multiple branch IDs
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }


    // Show the form for editing the specified user
    public function edit(User $user)
    {
        $branches = Branch::all(); // Fetch all branches for dropdown
        $userBranches = $user->branches->pluck('id')->toArray(); // Get the branch IDs the user belongs to
        $roleOptions = User::distinctRolesFromDatabase();
        if ($user->role !== null && $user->role !== '' && ! in_array($user->role, $roleOptions, true)) {
            $roleOptions[] = $user->role;
            natcasesort($roleOptions);
            $roleOptions = array_values($roleOptions);
        }

        return inertia('Admin/User/Edit', compact('user', 'branches', 'userBranches', 'roleOptions'));
    }

    // Update the specified user in storage
    public function update(Request $request, User $user)
    {
        $allowedRoles = User::distinctRolesFromDatabase();
        if ($user->role !== null && $user->role !== '' && ! in_array($user->role, $allowedRoles, true)) {
            $allowedRoles[] = $user->role;
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => ['required', 'string', 'max:255', Rule::in($allowedRoles)],
            'password' => 'nullable|string|min:8|confirmed',
            'branch_ids' => 'nullable|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        // Sync the selected branches with the user
        $user->branches()->sync($request->branch_ids ?? []); // Handle no branches as empty array

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    // Remove the specified user from storage
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}
