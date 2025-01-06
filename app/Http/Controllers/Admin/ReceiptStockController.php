<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OfficerReceiptDistribution;
use App\Models\ReceiptStock;
use App\Models\ReceiptTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceiptStockController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get stocks based on user's branch
        $stocks = ReceiptStock::with(['branch'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->latest()
            ->paginate(10);

        // For branch users, only return their branch
        $branches = Branch::select('id', 'branch_name')
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('id', $user->branch_id);
            })
            ->get();

        return Inertia::render('Admin/ReceiptStocks/Index', [
            'stocks' => $stocks,
            'branches' => $branches,
            'userBranch' => $user->branch_id,
            'flash' => [
                'message' => session('message'),
                'error' => session('error')
            ]
        ]);
    }

    public function distributions(Branch $branch)
    {
        $user = auth()->user();

        // Ensure user can only view distributions for their branch
        if ($user->branch_id && $user->branch_id !== $branch->id) {
            abort(403);
        }

        return OfficerReceiptDistribution::with(['officer', 'user'])
            ->where('branch_id', $branch->id)
            ->latest('distribution_date')
            ->get();
    }

    public function transfer(Request $request)
    {
        $user = auth()->user();

        try {
            $validated = $request->validate([
                'quantity' => 'required|integer|min:1',
                'notes' => 'nullable|string',
                'transfer_date' => 'required|date',
            ]);

            // If user has branch_id, use that instead of input
            $validated['to_branch_id'] = $user->branch_id;

            // Ensure user has permission to add stock to this branch
            if (!$user->branch_id) {
                return redirect()->back()
                    ->withErrors(['error' => 'Unauthorized to add stock to this branch'])
                    ->withInput();
            }

            DB::transaction(function () use ($validated, $user) {
                // Record the stock addition from Head Office
                ReceiptTransfer::create([
                    'from_branch_id' => null, // Head Office
                    'to_branch_id' => $validated['to_branch_id'],
                    'user_id' => $user->id,
                    'quantity' => $validated['quantity'],
                    'notes' => $validated['notes'],
                    'transfer_date' => $validated['transfer_date'],
                ]);

                // Add stock to branch
                $stock = ReceiptStock::firstOrCreate(
                    ['branch_id' => $validated['to_branch_id']],
                    ['total_receipts' => 0, 'used_receipts' => 0, 'available_receipts' => 0]
                );

                $stock->increment('total_receipts', $validated['quantity']);
                $stock->increment('available_receipts', $validated['quantity']);
            });

            return redirect()->back()->with('message', 'Stock added successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to add stock: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function history(Branch $branch)
    {
        $user = auth()->user();

        // Ensure user can only view history for their branch
        if ($user->branch_id && $user->branch_id !== $branch->id) {
            abort(403);
        }

        return ReceiptTransfer::with(['toBranch', 'user'])
            ->where('to_branch_id', $branch->id)
            ->where('from_branch_id', null) // Only Head Office transfers
            ->latest('transfer_date')
            ->get();
    }
}
