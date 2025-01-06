<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchOfficer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchOfficerController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $officers = BranchOfficer::with(['branch'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/BranchOfficers/Index', [
            'officers' => $officers,
            'branches' => Branch::select('id', 'branch_name')->get(),
            'userBranch' => $user->branch_id,
            'flash' => [
                'message' => session('message'),
                'error' => session('error')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Log the incoming request data
        \Log::info('Incoming request data:', $request->all());
        \Log::info('User branch_id:', [$user->branch_id]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'pin_number' => 'required|string|unique:branch_officers,pin_number',
            'details' => 'nullable|string',
        ]);

        // If user has branch_id, use that instead of input
        if ($user->branch_id) {
            $validated['branch_id'] = $user->branch_id;
        }

        // Log the validated data
        \Log::info('Validated data:', $validated);

        try {
            $branchOfficer = BranchOfficer::create($validated);
            return redirect()->back()
                ->with('message', 'Officer created successfully');
        } catch (\Exception $e) {
            \Log::error('Error creating branch officer:', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create officer: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function update(Request $request, BranchOfficer $officer)
    {
        $user = auth()->user();

        // Ensure user can only update officers from their branch
        if ($user->branch_id && $officer->branch_id !== $user->branch_id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'pin_number' => 'required|string|unique:branch_officers,pin_number,' . $officer->id,
            'details' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $officer->update($validated);

        return redirect()->back()
            ->with('message', 'Officer updated successfully');
    }

    public function getByBranch(Branch $branch)
    {
        return BranchOfficer::where('branch_id', $branch->id)
            ->where('is_active', true)
            ->get();
    }
}
