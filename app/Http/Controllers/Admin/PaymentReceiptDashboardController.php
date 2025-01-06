<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchOfficer;
use App\Models\ReceiptStock;
use App\Models\ReceiptTransfer;
use App\Models\OfficerReceiptDistribution;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PaymentReceiptDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get authenticated user and their branch access
        $user = Auth::user();
        $userBranches = $user->branches->pluck('id');

        // Handle date filters safely
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Base query for branches based on user access
        $branchQuery = Branch::query();
        if ($userBranches->isNotEmpty()) {
            $branchQuery->whereIn('id', $userBranches);
        }

        // Get accessible branches
        $branches = $branchQuery->with(['receiptStock'])->get();
        $branchIds = $branches->pluck('id');

        // Get summary statistics
        $statistics = $this->getStatistics($branchIds, $startDate, $endDate);

        // Get recent activities
        $recentActivities = $this->getRecentActivities($branchIds, $startDate, $endDate);

        return Inertia::render('Admin/PaymentReceiptDashboard', [
            'statistics' => $statistics,
            'recentActivities' => $recentActivities,
            'branches' => $branches,
            'filters' => [
                'startDate' => $startDate->toDateString(),
                'endDate' => $endDate->toDateString(),
            ],
            'links' => [
                'branchOfficers' => route('admin.branch-officers.index'),
                'receiptStocks' => route('admin.receipt-stocks.index'),
                'createDistribution' => route('admin.receipt-distributions.create'),
            ]
        ]);
    }

    private function getStatistics($branchIds, Carbon $startDate, Carbon $endDate)
    {
        // Get active officers count within date range
        $activeOfficers = BranchOfficer::whereIn('branch_id', $branchIds)
            ->where('is_active', true)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Get receipt statistics within date range
        $receiptStats = ReceiptStock::whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('
                COALESCE(SUM(total_receipts), 0) as total,
                COALESCE(SUM(used_receipts), 0) as used,
                COALESCE(SUM(available_receipts), 0) as available
            ')->first();

        return [
            'branches' => Branch::whereIn('id', $branchIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'activeOfficers' => $activeOfficers,
            'receipts' => [
                'total' => $receiptStats->total ?? 0,
                'used' => $receiptStats->used ?? 0,
                'available' => $receiptStats->available ?? 0,
            ]
        ];
    }

    private function getRecentActivities($branchIds, Carbon $startDate, Carbon $endDate)
    {
        // Get recent transfers
        $transfers = ReceiptTransfer::with(['fromBranch:id,branch_name', 'toBranch:id,branch_name', 'user:id,name'])
            ->where(function ($query) use ($branchIds) {
                $query->whereIn('from_branch_id', $branchIds)
                    ->orWhereIn('to_branch_id', $branchIds);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        // Get recent distributions
        $distributions = OfficerReceiptDistribution::with([
            'branch:id,branch_name',
            'officer:id,name',
            'user:id,name'
        ])
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        return [
            'transfers' => $transfers->map(function ($transfer) {
                return [
                    'id' => $transfer->id,
                    'from' => $transfer->fromBranch?->branch_name ?? 'System',
                    'to' => $transfer->toBranch->branch_name,
                    'quantity' => $transfer->quantity,
                    'date' => $transfer->created_at->toDateString(),
                    'by' => $transfer->user->name
                ];
            }),
            'distributions' => $distributions->map(function ($distribution) {
                return [
                    'id' => $distribution->id,
                    'branch' => $distribution->branch->branch_name,
                    'officer' => $distribution->officer->name,
                    'quantity' => $distribution->quantity,
                    'date' => $distribution->created_at->toDateString(),
                    'by' => $distribution->user->name
                ];
            })
        ];
    }
}
