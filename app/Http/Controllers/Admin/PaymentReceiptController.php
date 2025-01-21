<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Models\PaymentReceipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class PaymentReceiptController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->name === "Super Admin";

        // Set default date range to current month if not provided
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $selectedBranch = $request->input('branch_id');

        // Base query with date range
        $query = PaymentReceipt::query()
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        // Handle Super Admin vs Branch User
        if ($isSuperAdmin) {
            return $this->superAdminDashboard($request);
        } else {
            $query->where('branch_id', $user->branch_id);
            $branches = null;

            // Get single branch summary with historical data - Fixed Query
            $branchId = $user->branch_id;

            // First get the current available receipts
            $latestReceipt = PaymentReceipt::where('branch_id', $branchId)
                ->orderBy('id', 'desc')
                ->first();

            // Then get the summary data
            $summaryData = DB::table('payment_receipts')
                ->where('branch_id', $branchId)
                ->selectRaw('
                    SUM(CASE WHEN transaction_date BETWEEN ? AND ? THEN receive_quantity ELSE 0 END) as period_received,
                    SUM(CASE WHEN transaction_date BETWEEN ? AND ? THEN given_quantity ELSE 0 END) as period_distributed,
                    SUM(receive_quantity) as all_time_received,
                    SUM(given_quantity) as all_time_distributed
                ', [$startDate, $endDate, $startDate, $endDate])
                ->first();

            // Combine the data
            $branchSummaries = (object) [
                'period_received' => $summaryData->period_received ?? 0,
                'period_distributed' => $summaryData->period_distributed ?? 0,
                'all_time_received' => $summaryData->all_time_received ?? 0,
                'all_time_distributed' => $summaryData->all_time_distributed ?? 0,
                'current_available' => $latestReceipt->available_receipts ?? 0
            ];
        }

        $receipts = $query->with('branch:id,branch_name,branch_code')
            ->orderBy('transaction_date', 'asc')  // Changed to 'asc'
            ->orderBy('id', 'asc')               // Changed to 'asc'
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

    private function superAdminDashboard(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $selectedBranch = $request->input('branch_id');

        // Get all branches for dropdown
        $branches = Branch::select('id', 'branch_name')->get();

        // Prepare bindings array
        $bindings = [$startDate, $endDate];
        if ($selectedBranch) {
            $bindings[] = $selectedBranch;
        }

        // Get branch summaries with historical totals
        $branchSummaries = Branch::select(
            'branches.id as branch_id',
            'branches.branch_name',
            DB::raw('COALESCE(filtered.total_received, 0) as period_received'),
            DB::raw('COALESCE(filtered.total_distributed, 0) as period_distributed'),
            DB::raw('COALESCE(all_time.total_received, 0) as all_time_received'),
            DB::raw('COALESCE(all_time.total_distributed, 0) as all_time_distributed'),
            DB::raw('COALESCE(latest.available_receipts, 0) as current_available')
        )
            ->leftJoin(DB::raw("(
        SELECT
            branch_id,
            SUM(receive_quantity) as total_received,
            SUM(given_quantity) as total_distributed
        FROM payment_receipts
        WHERE transaction_date BETWEEN ? AND ?
        GROUP BY branch_id
    ) as filtered"), 'branches.id', '=', 'filtered.branch_id')
            ->leftJoin(DB::raw("(
        SELECT
            branch_id,
            SUM(receive_quantity) as total_received,
            SUM(given_quantity) as total_distributed
        FROM payment_receipts
        GROUP BY branch_id
    ) as all_time"), 'branches.id', '=', 'all_time.branch_id')
            ->leftJoin(DB::raw("(
        SELECT
            p1.branch_id,
            p1.available_receipts
        FROM payment_receipts p1
        INNER JOIN (
            SELECT branch_id, MAX(id) as max_id
            FROM payment_receipts
            GROUP BY branch_id
        ) p2 ON p1.branch_id = p2.branch_id AND p1.id = p2.max_id
    ) as latest"), 'branches.id', '=', 'latest.branch_id')
            ->when($selectedBranch, function ($q) {
                return $q->where('branches.id', '?');
            })
            ->setBindings($bindings)
            ->get();

        // Get receipts for selected branch or all branches
        $query = PaymentReceipt::query()
            ->with('branch:id,branch_name,branch_code')
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        }

        $receipts = $query->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('PaymentReceipts/SuperAdminIndex', [
            'receipts' => $receipts,
            'branchSummaries' => $branchSummaries,
            'branches' => $branches,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'branch_id' => $selectedBranch,
            ]
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

        // Base query for receipts with branch information
        $query = PaymentReceipt::query()
            ->with('branch:id,branch_name')
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            });

        // Apply branch filter based on user role
        if ($user->name === "Super Admin") {
            if ($selectedBranch) {
                $query->where('branch_id', $selectedBranch);
            }
        } else {
            $query->where('branch_id', $user->branch_id);
        }

        // Get the receipts for the report
        $receipts = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Get branch name for title
        $branchName = $selectedBranch
            ? Branch::find($selectedBranch)->branch_name
            : ($user->name === "Super Admin" ? 'All Branches' : $user->branch->branch_name);

        // Calculate period summary
        $periodSummary = [
            'received' => $receipts->sum('receive_quantity'),
            'distributed' => $receipts->sum('given_quantity')
        ];

        // Calculate all-time summary
        $allTimeQuery = PaymentReceipt::query();

        // Apply branch filter for all-time calculations
        if ($user->name === "Super Admin") {
            if ($selectedBranch) {
                $allTimeQuery->where('branch_id', $selectedBranch);
            }
        } else {
            $allTimeQuery->where('branch_id', $user->branch_id);
        }

        $allTimeReceipts = $allTimeQuery->get();

        $allTimeSummary = [
            'received' => $allTimeReceipts->sum('receive_quantity'),
            'distributed' => $allTimeReceipts->sum('given_quantity'),
            'available' => $allTimeReceipts->sum('receive_quantity') - $allTimeReceipts->sum('given_quantity')
        ];

        // Format dates for filename
        $formattedStartDate = date('Y-m-d', strtotime($startDate));
        $formattedEndDate = date('Y-m-d', strtotime($endDate));

        // Generate PDF
        $pdf = PDF::loadView('pdf.payment-receipts', [
            'receipts' => $receipts,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'branchName' => $branchName,
            'periodSummary' => $periodSummary,
            'allTimeSummary' => $allTimeSummary
        ]);

        // Sanitize branch name for filename
        $sanitizedBranchName = str_replace(['/', '\\', ' '], '_', $branchName);

        // Return the PDF for download
        return $pdf->download("payment_receipts_{$sanitizedBranchName}_{$formattedStartDate}_to_{$formattedEndDate}.pdf");
    }


    public function generateReport(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id'
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $branchId = $request->branch_id;

            // Build query bindings array first
            $bindings = [$startDate, $endDate];
            if ($branchId) {
                $bindings[] = $branchId;
            }

            // Get branch summaries
            $branchQuery = Branch::select(
                'branches.id',
                'branches.branch_name',
                DB::raw('COALESCE(filtered.total_received, 0) as period_received'),
                DB::raw('COALESCE(filtered.total_distributed, 0) as period_distributed'),
                DB::raw('COALESCE(all_time.total_received, 0) as all_time_received'),
                DB::raw('COALESCE(all_time.total_distributed, 0) as all_time_distributed'),
                DB::raw('COALESCE(latest.available_receipts, 0) as current_available')
            )
                ->leftJoin(DB::raw("(
                SELECT
                    branch_id,
                    SUM(receive_quantity) as total_received,
                    SUM(given_quantity) as total_distributed
                FROM payment_receipts
                WHERE transaction_date BETWEEN ? AND ?
                GROUP BY branch_id
            ) as filtered"), 'branches.id', '=', 'filtered.branch_id')
                ->leftJoin(DB::raw("(
                SELECT
                    branch_id,
                    SUM(receive_quantity) as total_received,
                    SUM(given_quantity) as total_distributed
                FROM payment_receipts
                GROUP BY branch_id
            ) as all_time"), 'branches.id', '=', 'all_time.branch_id')
                ->leftJoin(DB::raw("(
                SELECT p1.*
                FROM payment_receipts p1
                INNER JOIN (
                    SELECT branch_id, MAX(id) as max_id
                    FROM payment_receipts
                    GROUP BY branch_id
                ) p2 ON p1.branch_id = p2.branch_id AND p1.id = p2.max_id
            ) as latest"), 'branches.id', '=', 'latest.branch_id');

            // Apply branch filter if specified
            if ($branchId) {
                $branchQuery->where('branches.id', $branchId);
            }

            // Now set all bindings at once
            $branches = $branchQuery->setBindings($bindings)->get();

            // Get transactions
            $transactions = PaymentReceipt::with('branch:id,branch_name')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->when($branchId, function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
                ->orderBy('transaction_date', 'desc')
                ->get();

            // Calculate totals
            $totals = [
                'total_period_received' => $branches->sum('period_received'),
                'total_period_distributed' => $branches->sum('period_distributed'),
                'total_available' => $branches->sum('current_available'),
                'total_branches' => $branches->count(),
            ];

            $data = [
                'meta' => [
                    'start_date' => Carbon::parse($startDate)->format('d/m/Y'),
                    'end_date' => Carbon::parse($endDate)->format('d/m/Y'),
                    'generated_at' => now()->format('d/m/Y H:i:s'),
                    'branch' => $branchId ? $branches->first()->branch_name : 'All Branches'
                ],
                'branches' => $branches,
                'transactions' => $transactions,
                'totals' => $totals
            ];

            $pdf = Pdf::loadView('reports.payment-receipts', $data);

            // Generate filename
            $filename = 'payment_receipts_' .
                ($branchId ? strtolower(str_replace(' ', '_', $branches->first()->branch_name)) : 'all_branches') . '_' .
                Carbon::parse($startDate)->format('Y_m_d') . '_to_' .
                Carbon::parse($endDate)->format('Y_m_d') . '.pdf';

            return $pdf->setPaper('a4', 'landscape')->download($filename);
        } catch (\Exception $e) {
            \Log::error('Report Generation Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to generate report: ' . $e->getMessage()], 422);
        }
    }
}
