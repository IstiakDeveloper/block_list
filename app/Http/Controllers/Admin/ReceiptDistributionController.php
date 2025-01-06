<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OfficerReceiptDistribution;
use App\Models\ReceiptStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceiptDistributionController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        // Get branches based on user's branch_id
        $branches = Branch::when($user->branch_id, function ($query) use ($user) {
            return $query->where('id', $user->branch_id);
        })->get();

        // Get stocks based on user's branch_id
        $stocks = ReceiptStock::when($user->branch_id, function ($query) use ($user) {
            return $query->where('branch_id', $user->branch_id);
        })->get();

        // Get distributions based on user's branch_id
        $recentDistributions = OfficerReceiptDistribution::with(['branch', 'officer', 'user'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->latest('distribution_date')
            ->paginate(10);

        return Inertia::render('Admin/ReceiptDistributions/Create', [
            'branches' => $branches,
            'stocks' => $stocks,
            'recentDistributions' => $recentDistributions,
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

        $validated = $request->validate([
            'branch_officer_id' => 'required|exists:branch_officers,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'distribution_date' => 'required|date',
        ]);

        // If user has branch_id, use that instead of input
        if ($user->branch_id) {
            $validated['branch_id'] = $user->branch_id;
        }

        try {
            DB::transaction(function () use ($validated) {
                // Check available stock
                $stock = ReceiptStock::where('branch_id', $validated['branch_id'])->firstOrFail();

                if ($stock->available_receipts < $validated['quantity']) {
                    throw new \Exception('Insufficient receipt stock');
                }

                // Create distribution record
                OfficerReceiptDistribution::create([
                    'branch_id' => $validated['branch_id'],
                    'branch_officer_id' => $validated['branch_officer_id'],
                    'user_id' => auth()->id(),
                    'quantity' => $validated['quantity'],
                    'notes' => $validated['notes'],
                    'distribution_date' => $validated['distribution_date'],
                ]);

                // Update stock
                $stock->decrement('available_receipts', $validated['quantity']);
                $stock->increment('used_receipts', $validated['quantity']);
            });

            return redirect()->back()->with('message', 'Distribution completed successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
