<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Get all branches
     * GET /api/branches
     */
    public function index(Request $request)
    {
        $query = Branch::query();

        // Search functionality
        if ($request->has('search')) {
            $query->search($request->search);
        }

        // Sort functionality
        if ($request->has('sort_by')) {
            $direction = $request->get('sort_direction', 'asc');
            $query->sort($request->sort_by, $direction);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);

        if ($request->has('all') && $request->all == 'true') {
            $branches = $query->get();
            return response()->json([
                'success' => true,
                'data' => $branches
            ], 200);
        }

        $branches = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $branches->items(),
            'pagination' => [
                'current_page' => $branches->currentPage(),
                'per_page' => $branches->perPage(),
                'total' => $branches->total(),
                'last_page' => $branches->lastPage(),
            ]
        ], 200);
    }

    /**
     * Get single branch
     * GET /api/branches/{id}
     */
    public function show($id)
    {
        $branch = Branch::with(['users', 'customers', 'branchOfficers'])->find($id);

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $branch
        ], 200);
    }

    /**
     * Create new branch
     * POST /api/branches
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|unique:branches,branch_code',
            'address' => 'nullable|string',
        ]);

        $branch = Branch::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Branch created successfully',
            'data' => $branch
        ], 201);
    }

    /**
     * Update branch
     * PUT/PATCH /api/branches/{id}
     */
    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $request->validate([
            'branch_name' => 'sometimes|required|string|max:255',
            'branch_code' => 'sometimes|required|string|unique:branches,branch_code,' . $id,
            'address' => 'nullable|string',
        ]);

        $branch->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Branch updated successfully',
            'data' => $branch
        ], 200);
    }

    /**
     * Delete branch
     * DELETE /api/branches/{id}
     */
    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }

        $branch->delete();

        return response()->json([
            'success' => true,
            'message' => 'Branch deleted successfully'
        ], 200);
    }
}
