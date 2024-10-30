<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $branches = Branch::query()
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->when($request->sort, fn($q) => $q->sort(
                $request->sort,
                $request->input('direction', 'asc')
            ))
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return Inertia::render('Admin/Branch/Index', [
            'branches' => $branches,
            'filters' => $request->only(['search', 'sort', 'direction', 'per_page']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Render the create form view using Inertia
        return Inertia::render('Admin/Branch/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255|unique:branches',
            'address' => 'nullable|string',
        ]);

        // Create a new branch
        Branch::create($request->all());

        // Redirect to the index page with success message
        return redirect()->route('admin.branches.index')
                         ->with('success', 'Branch created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        // Render the edit form view using Inertia
        return Inertia::render('Admin/Branch/Edit', [
            'branch' => $branch,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        // Validate the request
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|max:255|unique:branches,branch_code,' . $branch->id,
            'address' => 'nullable|string',
        ]);

        // Update the branch with validated data
        $branch->update($request->all());

        // Redirect to the index page with success message
        return redirect()->route('admin.branches.index')
                         ->with('success', 'Branch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        // Delete the branch
        $branch->delete();

        // Redirect to the index page with success message
        return redirect()->route('admin.branches.index')
                         ->with('success', 'Branch deleted successfully.');
    }
}
