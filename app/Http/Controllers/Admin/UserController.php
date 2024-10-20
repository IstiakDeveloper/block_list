<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Show the list of users
    public function index()
    {
        $users = User::with('branch')->paginate(10); // Get all users with the associated branch
        return inertia('Admin/User/Index', compact('users'));
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
            'branch_id' => 'nullable|exists:branches,id', // Ensure the branch exists
        ]);

        // Create the user
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'branch_id' => $request->branch_id, // Assign the branch to the user
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully!');
    }

    // Show the form for editing the specified user
    public function edit(User $user)
    {
        $branches = Branch::all(); // Fetch all branches to populate the dropdown
        return inertia('Admin/User/Edit', compact('user', 'branches'));
    }

    // Update the specified user in storage
    public function update(Request $request, User $user)
    {


        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'branch_id' => $request->branch_id, // Assign the branch to the user
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully!');
    }

    // Remove the specified user from storage
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully!');
    }
}
