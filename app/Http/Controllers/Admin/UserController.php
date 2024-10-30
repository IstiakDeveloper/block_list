<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        return inertia('Admin/User/Create', compact('branches'));
    }

    // Store a newly created user in storage
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'branch_ids' => 'nullable|array', // Expect an array of branch IDs
            'branch_ids.*' => 'exists:branches,id', // Ensure each branch ID exists in the branches table
        ]);

        // Create the user without assigning branches immediately
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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

        return inertia('Admin/User/Edit', compact('user', 'branches', 'userBranches'));
    }

    // Update the specified user in storage
    public function update(Request $request, User $user)
    {
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'email' => 'required|email|unique:users,email,' . $user->id,
        //     'password' => 'nullable|string|min:8|confirmed',
        //     'branch_ids' => 'nullable|array',
        //     'branch_ids.*' => 'exists:branches,id',
        // ]);

        // Update user data
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
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
