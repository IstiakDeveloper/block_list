<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchOfficer;
use App\Models\ReceiptStock;
use App\Models\ReceiptTransfer;
use App\Models\OfficerReceiptDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class PaymentReceiptDashboardController extends Controller
{
    /**
     * Display the payment receipt dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $dateRange = $this->getDateRange($request);

        // Get accessible branches based on user's branch_id
        $branches = $this->getAccessibleBranches($user);
        $branchIds = $branches->pluck('id');

        // Fetch dashboard data with caching
        $cacheKey = "dashboard_{$user->id}_{$dateRange['start']}_{$dateRange['end']}";
        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($branchIds, $dateRange) {
            return [
                'statistics' => $this->getStatistics($branchIds, $dateRange['start'], $dateRange['end']),
                'recentActivities' => $this->getRecentActivities($branchIds, $dateRange['start'], $dateRange['end']),
            ];
        });

        return Inertia::render('Admin/PaymentReceiptDashboard', [
            'statistics' => $dashboardData['statistics'],
            'recentActivities' => $dashboardData['recentActivities'],
            'branches' => $branches,
            'filters' => [
                'startDate' => $dateRange['start']->toDateString(),
                'endDate' => $dateRange['end']->toDateString(),
            ],
            'links' => $this->getDashboardLinks(),
            'userBranch' => $user->branch_id // Pass user's branch_id to frontend
        ]);
    }

    /**
     * Get accessible branches based on user's branch_id.
     */
    private function getAccessibleBranches($user)
    {
        return Branch::query()
            ->when($user->branch_id, function (Builder $query) use ($user) {
                $query->where('id', $user->branch_id);
            })
            ->with([
                'receiptStock' => function ($query) {
                    $query->select('branch_id', 'total_receipts', 'available_receipts', 'used_receipts');
                }
            ])
            ->get(['id', 'branch_name']);
    }

    /**
     * Get date range from request or defaults.
     */
    private function getDateRange(Request $request): array
    {
        return [
            'start' => $request->input('start_date')
                ? Carbon::parse($request->input('start_date'))->startOfDay()
                : Carbon::now()->startOfMonth(),
            'end' => $request->input('end_date')
                ? Carbon::parse($request->input('end_date'))->endOfDay()
                : Carbon::now()->endOfDay()
        ];
    }

    /**
     * Get statistics for the dashboard.
     */
    private function getStatistics($branchIds, Carbon $startDate, Carbon $endDate): array
    {
        $activeOfficers = BranchOfficer::whereIn('branch_id', $branchIds)
            ->where('is_active', true)  // Keep this as it's for officers, not branches
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $receiptStats = ReceiptStock::whereIn('branch_id', $branchIds)
            ->selectRaw('
            COALESCE(SUM(total_receipts), 0) as total,
            COALESCE(SUM(used_receipts), 0) as used,
            COALESCE(SUM(available_receipts), 0) as available
        ')
            ->first();

        // Calculate percentage changes
        $previousPeriodStats = $this->getPreviousPeriodStats($branchIds, $startDate, $endDate);

        return [
            'branches' => [
                'total' => $branchIds->count(),  // Removed is_active check
            ],
            'activeOfficers' => $activeOfficers,
            'receipts' => [
                'total' => $receiptStats->total ?? 0,
                'used' => $receiptStats->used ?? 0,
                'available' => $receiptStats->available ?? 0,
                'changes' => $this->calculatePercentageChanges($receiptStats, $previousPeriodStats)
            ]
        ];
    }

    /**
     * Get statistics from the previous period for comparison.
     */
    private function getPreviousPeriodStats($branchIds, Carbon $startDate, Carbon $endDate): object
    {
        $periodDays = $startDate->diffInDays($endDate);
        $previousStart = $startDate->copy()->subDays($periodDays);
        $previousEnd = $endDate->copy()->subDays($periodDays);

        return ReceiptStock::whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->selectRaw('
                COALESCE(SUM(total_receipts), 0) as total,
                COALESCE(SUM(used_receipts), 0) as used,
                COALESCE(SUM(available_receipts), 0) as available
            ')
            ->first();
    }

    /**
     * Calculate percentage changes from previous period.
     */
    private function calculatePercentageChanges($current, $previous): array
    {
        $calculateChange = function ($current, $previous) {
            if (!$previous)
                return null;
            return $previous ? round((($current - $previous) / $previous) * 100, 1) : null;
        };

        return [
            'total' => $calculateChange($current->total, $previous->total),
            'used' => $calculateChange($current->used, $previous->used),
            'available' => $calculateChange($current->available, $previous->available)
        ];
    }

    /**
     * Get recent activities for the dashboard.
     */
    private function getRecentActivities($branchIds, Carbon $startDate, Carbon $endDate): array
    {
        $transfers = ReceiptTransfer::with(['fromBranch:id,branch_name', 'toBranch:id,branch_name', 'user:id,name'])
            ->where(function ($query) use ($branchIds) {
                $query->whereIn('from_branch_id', $branchIds)
                    ->orWhereIn('to_branch_id', $branchIds);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        $distributions = OfficerReceiptDistribution::with([
            'branch:id,branch_name',
            'officer:id,name,pin_number',
            'user:id,name'
        ])
            ->whereIn('branch_id', $branchIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        return [
            'transfers' => $transfers->map(fn($transfer) => [
                'id' => $transfer->id,
                'from' => $transfer->fromBranch?->branch_name ?? 'System',
                'to' => $transfer->toBranch->branch_name,
                'quantity' => $transfer->quantity,
                'date' => $transfer->created_at->toDateString(),
                'time' => $transfer->created_at->format('H:i'),
                'by' => $transfer->user->name
            ]),
            'distributions' => $distributions->map(fn($distribution) => [
                'id' => $distribution->id,
                'branch' => $distribution->branch->branch_name,
                'officer' => [
                    'name' => $distribution->officer->name,
                    'pin' => $distribution->officer->pin_number
                ],
                'quantity' => $distribution->quantity,
                'date' => $distribution->created_at->toDateString(),
                'time' => $distribution->created_at->format('H:i'),
                'by' => $distribution->user->name
            ])
        ];
    }

    /**
     * Get dashboard navigation links.
     */
    private function getDashboardLinks(): array
    {
        return [
            'branchOfficers' => route('admin.branch-officers.index'),
            'receiptStocks' => route('admin.receipt-stocks.index'),
            'createDistribution' => route('admin.receipt-distributions.create'),
            // 'reports' => route('admin.receipt-reports.index')
        ];
    }
}
