<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\PaymentReceipt;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->name === "Super Admin";

        // Get date filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedBranch = $request->input('branch_id');

        // Base query
        $query = PaymentReceipt::query();

        // Apply date filters if provided
        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        // Handle Super Admin vs Branch User
        if ($isSuperAdmin) {
            // For Super Admin: Show all branches or filter by selected branch
            if ($selectedBranch) {
                $query->where('branch_id', $selectedBranch);
            }

            // Get all branches for dropdown
            $branches = Branch::select('id', 'branch_name')->get();

            // Get summary for all branches or selected branch
            $branchSummaries = PaymentReceipt::when($selectedBranch, function ($q) use ($selectedBranch) {
                    $q->where('branch_id', $selectedBranch);
                })
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                })
                ->select(
                    'branch_id',
                    DB::raw('SUM(receive_quantity) as total_received'),
                    DB::raw('SUM(given_quantity) as total_distributed'),
                    DB::raw('MAX(total_cumulative_quantity) as cumulative_total')
                )
                ->groupBy('branch_id')
                ->with('branch:id,branch_name')
                ->get();

            // Get the latest available receipts for each branch
            $latestAvailable = PaymentReceipt::select('branch_id', 'available_receipts')
                ->whereIn('id', function ($query) {
                    $query->select(DB::raw('MAX(id)'))
                        ->from('payment_receipts')
                        ->groupBy('branch_id');
                })
                ->get()
                ->pluck('available_receipts', 'branch_id');

            // Combine summaries with latest available receipts
            $branchSummaries = $branchSummaries->map(function ($summary) use ($latestAvailable) {
                $summary->current_available = $latestAvailable[$summary->branch_id] ?? 0;
                return $summary;
            });

        } else {
            // For Branch User: Only show their branch
            $query->where('branch_id', $user->branch_id);
            $branches = null;

            // Get summary for single branch
            $branchSummaries = PaymentReceipt::where('branch_id', $user->branch_id)
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                })
                ->select(
                    DB::raw('SUM(receive_quantity) as total_received'),
                    DB::raw('SUM(given_quantity) as total_distributed'),
                    DB::raw('MAX(total_cumulative_quantity) as cumulative_total'),
                    DB::raw('(SELECT available_receipts FROM payment_receipts WHERE branch_id = ' . $user->branch_id . ' ORDER BY id DESC LIMIT 1) as current_available')
                )
                ->first();
        }

        // Get paginated results
        $receipts = $query->with('branch:id,branch_name,branch_code')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('PaymentReceipts/Index', [
            'receipts' => $receipts,
            'branchSummaries' => $branchSummaries,
            'branches' => $branches,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'branch_id' => $selectedBranch,
            ],
            'isSuperAdmin' => $isSuperAdmin
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            // Get the latest record for calculations
            $latestRecord = PaymentReceipt::where('branch_id', auth()->user()->branch_id)
                ->latest()
                ->first();

            // Initialize values
            $receiveQuantity = $validated['receive_quantity'] ?? 0;
            $givenQuantity = $validated['given_quantity'] ?? 0;
            $currentAvailable = $latestRecord?->available_receipts ?? 0;

            // Calculate new totals
            $totalCumulative = ($latestRecord?->total_cumulative_quantity ?? 0) + $receiveQuantity;

            // Calculate new available receipts by adding new receipts and subtracting distributions
            $newAvailableReceipts = $currentAvailable + $receiveQuantity - $givenQuantity;

            // Validate if we have enough receipts when distributing
            if ($givenQuantity > 0) {
                $totalAvailable = $currentAvailable + $receiveQuantity;
                if ($givenQuantity > $totalAvailable) {
                    throw ValidationException::withMessages([
                        'given_quantity' => "Not enough receipts available. Current available: {$currentAvailable}, New receipts: {$receiveQuantity}, Total available: {$totalAvailable}"
                    ]);
                }
            }

            // Create the new record
            PaymentReceipt::create([
                'branch_id' => auth()->user()->branch_id,
                'transaction_date' => $validated['transaction_date'],
                'receive_quantity' => $receiveQuantity,
                'receipt_from_number' => $validated['receipt_from_number'] ?? null,
                'receipt_to_number' => $validated['receipt_to_number'] ?? null,
                'total_cumulative_quantity' => $totalCumulative,
                'received_by' => $validated['received_by'] ?? null,
                'given_to' => $validated['given_to'] ?? null,
                'pin_number' => $validated['pin_number'] ?? null,
                'given_from_number' => $validated['given_from_number'] ?? null,
                'given_to_number' => $validated['given_to_number'] ?? null,
                'receipt_book_number' => $validated['receipt_book_number'] ?? null,
                'given_quantity' => $givenQuantity,
                'available_receipts' => $newAvailableReceipts
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Receipt record created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validateRequest(Request $request)
    {
        return $request->validate([
            'transaction_date' => 'nullable|date',

            // Receiving section
            'receive_quantity' => 'nullable|integer|min:0',
            'receipt_from_number' => 'nullable|integer|min:1',
            'receipt_to_number' => 'nullable|integer|min:1',
            'received_by' => 'nullable|string|max:255',

            // Distribution section
            'given_quantity' => 'nullable|integer|min:0',
            'given_to' => 'nullable|string|max:255',
            'pin_number' => 'nullable|string|max:50',
            'given_from_number' => 'nullable|integer|min:1',
            'given_to_number' => 'nullable|integer|min:1',
            'receipt_book_number' => 'nullable|string|max:50',
        ]);
    }

    public function getBranchSummary(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $branchId = $user->name === "Super Admin" ? $request->input('branch_id') : $user->branch_id;

        $query = PaymentReceipt::query();

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        }

        return $query->select(
            DB::raw('SUM(receive_quantity) as total_received'),
            DB::raw('SUM(given_quantity) as total_distributed'),
            DB::raw('MAX(total_cumulative_quantity) as cumulative_total'),
            DB::raw('(SELECT available_receipts FROM payment_receipts WHERE branch_id = ' . $branchId . ' ORDER BY id DESC LIMIT 1) as current_available')
        )->first();
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedBranch = $request->input('branch_id');

        $query = PaymentReceipt::query()
            ->with('branch:id,name')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            });

        if ($user->name === "Super Admin") {
            if ($selectedBranch) {
                $query->where('branch_id', $selectedBranch);
            }
        } else {
            $query->where('branch_id', $user->branch_id);
        }

        $receipts = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Implementation of Excel export will go here
        // Return the file download response
    }
}
