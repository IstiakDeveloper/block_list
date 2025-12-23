<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentReceipt;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReceiptTransactionReportController extends Controller
{
    /**
     * Display the receipt transaction report page
     */
    public function index(Request $request)
    {
        $query = PaymentReceipt::with('branch')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        // Apply date filters if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        // Apply branch filter if provided
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->paginate(15)->withQueryString();

        // Get all branches for filter dropdown
        $branches = Branch::orderBy('branch_name')->get();

        // Calculate summary statistics
        $summary = $this->calculateSummary($request);

        return Inertia::render('Admin/Reports/ReceiptTransactionReport', [
            'transactions' => $transactions,
            'branches' => $branches,
            'filters' => $request->only(['start_date', 'end_date', 'branch_id']),
            'summary' => $summary
        ]);
    }

    /**
     * Get filtered data for the report
     */
    public function getFilteredData(Request $request)
    {
        $query = PaymentReceipt::with('branch')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');

        // Apply date filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        // Apply branch filter
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->get();

        // Group by date and type (received vs distributed)
        $reportData = $this->formatReportData($transactions);

        return response()->json([
            'data' => $reportData,
            'summary' => $this->calculateSummary($request)
        ]);
    }

    /**
     * Export report to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = PaymentReceipt::with('branch')
            ->orderBy('transaction_date', 'asc')
            ->orderBy('id', 'asc');

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->get();
        $reportData = $this->formatReportData($transactions);
        $summary = $this->calculateSummary($request);

        // Get branch name if filtered
        $branchName = 'All Branches';
        if ($request->has('branch_id') && $request->branch_id) {
            $branch = Branch::find($request->branch_id);
            $branchName = $branch ? $branch->branch_name : 'All Branches';
        }

        $pdf = PDF::loadView('reports.receipt-transaction-pdf', [
            'reportData' => $reportData,
            'summary' => $summary,
            'filters' => [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'branch_name' => $branchName
            ],
            'generatedAt' => now()->format('d-m-Y H:i:s')
        ]);

        $filename = 'receipt-transaction-report-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Format transaction data for report (Month-wise grouped)
     */
    private function formatReportData($transactions)
    {
        $monthData = [];

        foreach ($transactions as $transaction) {
            $monthKey = $transaction->transaction_date->format('Y-m'); // Group by month
            $dateKey = $transaction->transaction_date->format('Y-m-d');

            if (!isset($monthData[$monthKey])) {
                $monthData[$monthKey] = [
                    'month_name' => $transaction->transaction_date->format('F Y'),
                    'dates' => []
                ];
            }

            if (!isset($monthData[$monthKey]['dates'][$dateKey])) {
                $monthData[$monthKey]['dates'][$dateKey] = [
                    'date' => $transaction->transaction_date->format('d-m-Y'),
                    'day_name' => $transaction->transaction_date->format('l'),
                    'received' => [],
                    'distributed' => []
                ];
            }

            // Check if this is a receive transaction or distribution
            if ($transaction->receive_quantity > 0 && !$transaction->given_to) {
                // This is a receive transaction (branch receiving from head office)
                $monthData[$monthKey]['dates'][$dateKey]['received'][] = [
                    'id' => $transaction->id,
                    'branch_name' => $transaction->branch->branch_name ?? 'N/A',
                    'quantity' => $transaction->receive_quantity,
                    'from_number' => $transaction->receipt_from_number,
                    'to_number' => $transaction->receipt_to_number,
                    'received_by' => $transaction->received_by,
                    'available' => $transaction->available_receipts
                ];
            }

            if ($transaction->given_quantity > 0 && $transaction->given_to) {
                // This is a distribution transaction (branch giving to officer)
                $monthData[$monthKey]['dates'][$dateKey]['distributed'][] = [
                    'id' => $transaction->id,
                    'branch_name' => $transaction->branch->branch_name ?? 'N/A',
                    'quantity' => $transaction->given_quantity,
                    'from_number' => $transaction->given_from_number,
                    'to_number' => $transaction->given_to_number,
                    'given_to' => $transaction->given_to,
                    'pin_number' => $transaction->pin_number,
                    'receipt_book_number' => $transaction->receipt_book_number
                ];
            }
        }

        return $monthData;
    }

    /**
     * Calculate summary statistics
     */
    private function calculateSummary($request)
    {
        $query = PaymentReceipt::query();

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->get();

        $totalReceived = $transactions->sum('receive_quantity');
        $totalDistributed = $transactions->sum('given_quantity');
        $totalAvailable = $transactions->sum('available_receipts');

        return [
            'total_received' => $totalReceived,
            'total_distributed' => $totalDistributed,
            'total_available' => $totalAvailable,
            'total_transactions' => $transactions->count()
        ];
    }

    /**
     * Get date-wise summary
     */
    public function getDatewiseSummary(Request $request)
    {
        $query = PaymentReceipt::with('branch')
            ->orderBy('transaction_date', 'desc');

        // Apply filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('transaction_date', '<=', $request->end_date);
        }

        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $transactions = $query->get();

        // Group by date
        $summary = $transactions->groupBy(function($item) {
            return $item->transaction_date->format('Y-m-d');
        })->map(function($dayTransactions) {
            return [
                'date' => $dayTransactions->first()->transaction_date->format('d-m-Y'),
                'total_received' => $dayTransactions->sum('receive_quantity'),
                'total_distributed' => $dayTransactions->sum('given_quantity'),
                'total_available' => $dayTransactions->sum('available_receipts'),
                'transaction_count' => $dayTransactions->count()
            ];
        })->values();

        return response()->json($summary);
    }
}
