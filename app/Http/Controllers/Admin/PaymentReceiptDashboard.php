<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchOfficer;
use App\Models\ReceiptStock;
use App\Models\OfficerReceiptDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class PaymentReceiptDashboard extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from'
        ]);

        $user = auth()->user();

        $dateFrom = $request->filled('date_from')
            ? Carbon::parse($request->date_from)->startOfDay()
            : now()->subDays(30)->startOfDay();

        $dateTo = $request->filled('date_to')
            ? Carbon::parse($request->date_to)->endOfDay()
            : now()->endOfDay();

        // Get stock summary without the problematic exists clause
        $stockSummary = ReceiptStock::when($user->branch_id, function ($query) use ($user) {
            return $query->where('branch_id', $user->branch_id);
        })
            ->with(['branch'])
            ->select(
                'branch_id',
                DB::raw('SUM(total_receipts) as total_receipts'),
                DB::raw('SUM(used_receipts) as used_receipts'),
                DB::raw('SUM(available_receipts) as available_receipts')
            )
            ->groupBy('branch_id')
            ->get();

        // Get officer distributions with date range
        $officerDistributions = OfficerReceiptDistribution::with(['officer', 'branch'])
            ->when($user->branch_id, function ($query) use ($user) {
                return $query->where('branch_id', $user->branch_id);
            })
            ->whereBetween('distribution_date', [$dateFrom, $dateTo])
            ->select(
                'branch_officer_id',
                'branch_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('COUNT(*) as distribution_count')
            )
            ->groupBy('branch_officer_id', 'branch_id')
            ->orderByDesc('total_quantity')
            ->get();

        // Get daily distribution trend
        $dailyDistributions = OfficerReceiptDistribution::when($user->branch_id, function ($query) use ($user) {
            return $query->where('branch_id', $user->branch_id);
        })
            ->whereBetween('distribution_date', [$dateFrom, $dateTo])
            ->select(
                DB::raw('DATE(distribution_date) as date'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Calculate totals
        $totalReceipts = $stockSummary->sum('total_receipts');
        $usedReceipts = $stockSummary->sum('used_receipts');

        return Inertia::render('Admin/PaymentReceiptDashboard', [
            'stockSummary' => $stockSummary,
            'officerDistributions' => $officerDistributions,
            'dailyDistributions' => $dailyDistributions,
            'dateRange' => [
                'from' => $dateFrom->format('Y-m-d'),
                'to' => $dateTo->format('Y-m-d')
            ],
            'totalStock' => [
                'total' => $totalReceipts,
                'used' => $usedReceipts,
                'available' => $totalReceipts - $usedReceipts
            ],
            'quickLinks' => [
                [
                    'name' => 'Branch Officers',
                    'route' => route('admin.branch-officers.index'),
                    'icon' => 'UserGroupIcon'
                ],
                [
                    'name' => 'Receipt Stock',
                    'route' => route('admin.receipt-stocks.index'),
                    'icon' => 'ArchiveBoxIcon'
                ],
                [
                    'name' => 'Distribute Receipts',
                    'route' => route('admin.receipt-distributions.create'),
                    'icon' => 'ShareIcon'
                ]
            ]
        ]);
    }
}
