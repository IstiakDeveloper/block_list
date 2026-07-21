<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $dateRange = $request->input('dateRange', 'all');
        $startDate = null;
        $endDate = null;

        // Get user's accessible branches
        $userBranches = $this->getUserBranches($user);

        // If user has only one branch and no branch filter is set, automatically set it
        if ($userBranches->count() === 1 && !$request->has('branch_filter')) {
            $request->merge(['branch_filter' => $userBranches->first()->id]);
        }

        if ($dateRange === 'custom') {
            $startDate = $request->input('startDate') ? Carbon::parse($request->input('startDate'))->startOfDay() : null;
            $endDate = $request->input('endDate') ? Carbon::parse($request->input('endDate'))->endOfDay() : null;
        } else {
            $startDate = $this->getStartDate($dateRange);
        }

        // Build branch query with user access control
        $branchQuery = Branch::with(['users']);

        // Apply branch filtering based on user permissions
        $this->applyBranchFilter($branchQuery, $user, $userBranches, $request->input('branch_filter'));

        $branchDetails = $branchQuery->withCount([
            'customers as total_customers' => function ($query) use ($startDate, $endDate) {
                $this->applyDateFilter($query, $startDate, $endDate);
            }
        ])
            ->get()
            ->map(function ($branch) use ($dateRange, $startDate, $endDate) {
                $userEntriesQuery = Customer::where('branch_id', $branch->id)
                    ->select('user_id', DB::raw('COUNT(*) as entry_count'));

                $this->applyDateFilter($userEntriesQuery, $startDate, $endDate);

                $userEntries = $userEntriesQuery->groupBy('user_id')
                    ->with('user:id,name,role')
                    ->get()
                    ->map(function ($entry) {
                        return [
                            'user_id' => $entry->user->id,
                            'name' => $entry->user->name,
                            'entries' => $entry->entry_count,
                            'role' => $entry->user->role
                        ];
                    });

                return [
                    'id' => $branch->id,
                    'name' => $branch->branch_name,
                    'code' => $branch->branch_code,
                    'total_customers' => $branch->total_customers,
                    'users' => $userEntries,
                    'this_month' => $this->getBranchCount($branch->id, 'month'),
                    'last_7_days' => $this->getBranchCount($branch->id, 'week'),
                    'all_time' => $this->getBranchCount($branch->id, 'all')
                ];
            });

        $filterData = [
            'dateRange' => $dateRange,
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'branch_filter' => $request->input('branch_filter')
        ];

        return Inertia::render('Admin/Reports/Dashboard', [
            'reportData' => array_merge(
                $this->getReportData($branchDetails, $dateRange, $startDate, $endDate, $user, $userBranches, $request->input('branch_filter')),
                ['filter' => $filterData]
            ),
            'userBranches' => $userBranches
        ]);
    }

    /**
     * Get user's accessible branches based on role
     */
    private function getUserBranches($user)
    {
        return $user->authorizedBranches();
    }

    /**
     * Apply branch filtering based on user permissions
     */
    private function applyBranchFilter($query, $user, $userBranches, $branchFilter = null)
    {
        if ($branchFilter) {
            // For Super Admin, allow filtering by any branch
            if ($user->name === 'Super Admin') {
                $query->where('id', $branchFilter);
            }
            // For other users, only allow filtering by their assigned branches
            else {
                $userBranchIds = $userBranches->pluck('id');
                if ($userBranchIds->contains($branchFilter)) {
                    $query->where('id', $branchFilter);
                }
            }
        }
        // If no branch filter is applied, show only accessible branches
        else {
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                $query->whereIn('id', $userBranchIds);
            }
        }
    }

    /**
     * Apply customer filtering based on user branch access
     */
    private function applyCustomerBranchFilter($query, $user, $userBranches, $branchFilter = null)
    {
        if ($branchFilter) {
            // For Super Admin, allow filtering by any branch
            if ($user->name === 'Super Admin') {
                $query->where('branch_id', $branchFilter);
            }
            // For other users, only allow filtering by their assigned branches
            else {
                $userBranchIds = $userBranches->pluck('id');
                if ($userBranchIds->contains($branchFilter)) {
                    $query->where('branch_id', $branchFilter);
                }
            }
        }
        // If no branch filter is applied, show only accessible branches
        else {
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                $query->whereIn('branch_id', $userBranchIds);
            }
        }
    }

    private function getStartDate($range)
    {
        return match ($range) {
            'week' => now()->subDays(7)->startOfDay(),
            'month' => now()->startOfMonth(),
            'custom' => request('startDate') ? Carbon::parse(request('startDate'))->startOfDay() : null,
            default => null
        };
    }

    /**
     * Start/end of the "current period" for PDF summary columns (matches dashboard date filter).
     * "Current Month" column = blocks within [start, end]; "Before Block" = strictly before start;
     * "Total" = cumulative through end (created_at <= end).
     */
    private function resolveDownloadReportPeriod(Request $request): array
    {
        $dateRange = $request->input('dateRange', 'all');

        if ($dateRange === 'custom') {
            $start = $request->filled('startDate')
                ? Carbon::parse($request->input('startDate'))->startOfDay()
                : null;
            $end = $request->filled('endDate')
                ? Carbon::parse($request->input('endDate'))->endOfDay()
                : null;

            if (!$start) {
                return ['start' => null, 'end' => null, 'label' => 'All time'];
            }
            if (!$end) {
                $end = now()->copy()->endOfDay();
            }

            return [
                'start' => $start,
                'end' => $end,
                'label' => $start->format('d/m/Y') . ' – ' . $end->format('d/m/Y'),
            ];
        }

        if ($dateRange === 'month') {
            $start = now()->copy()->startOfMonth();
            $end = now()->copy()->endOfDay();

            return [
                'start' => $start,
                'end' => $end,
                'label' => $start->format('F Y'),
            ];
        }

        if ($dateRange === 'week') {
            $start = now()->copy()->subDays(7)->startOfDay();
            $end = now()->copy()->endOfDay();

            return [
                'start' => $start,
                'end' => $end,
                'label' => $start->format('d/m/Y') . ' – ' . $end->format('d/m/Y'),
            ];
        }

        return ['start' => null, 'end' => null, 'label' => 'All time'];
    }

    private function getReportData($branchDetails, $dateRange, $startDate, $endDate, $user, $userBranches, $branchFilter = null)
    {
        $customerQuery = Customer::query();
        $this->applyDateFilter($customerQuery, $startDate, $endDate);
        $this->applyCustomerBranchFilter($customerQuery, $user, $userBranches, $branchFilter);

        // Build branch query for report data
        $branchWiseQuery = Branch::query();
        $this->applyBranchFilter($branchWiseQuery, $user, $userBranches, $branchFilter);

        $branchWiseCustomers = $branchWiseQuery->withCount([
            'customers' => function ($query) use ($startDate, $endDate) {
                $this->applyDateFilter($query, $startDate, $endDate);
            }
        ])->get();

        // Recent customers query with branch filtering
        $recentCustomersQuery = Customer::with(['branch', 'user']);
        $this->applyCustomerBranchFilter($recentCustomersQuery, $user, $userBranches, $branchFilter);

        return [
            'totalCustomers' => $customerQuery->count(),
            'totalBranches' => $userBranches->count(),
            'branchWiseCustomers' => $branchWiseCustomers,
            'branchDetails' => $branchDetails,
            'recentCustomers' => $recentCustomersQuery->latest()->take(5)->get(),
            'monthlyCustomers' => $this->getMonthlyData($startDate, $endDate, $user, $userBranches, $branchFilter),
            'ageDistribution' => $this->getAgeDistribution($startDate, $endDate, $user, $userBranches, $branchFilter)
        ];
    }

    private function getMonthlyData($startDate, $endDate, $user, $userBranches, $branchFilter = null)
    {
        $query = Customer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month');

        $this->applyDateFilter($query, $startDate, $endDate);
        $this->applyCustomerBranchFilter($query, $user, $userBranches, $branchFilter);

        return $query->get();
    }

    private function getAgeDistribution($startDate, $endDate, $user, $userBranches, $branchFilter = null)
    {
        $query = Customer::selectRaw('
            CASE
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 25 THEN "18-24"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 35 THEN "25-34"
                WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 45 THEN "35-44"
                ELSE "45+"
            END as age_group,
            COUNT(*) as count
        ')
            ->whereNotNull('dob');

        $this->applyDateFilter($query, $startDate, $endDate);
        $this->applyCustomerBranchFilter($query, $user, $userBranches, $branchFilter);

        return $query->groupBy('age_group')->get();
    }

    private function applyDateFilter($query, $startDate, $endDate)
    {
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }
    }

    private function getBranchCount($branchId, $range)
    {
        $query = Customer::where('branch_id', $branchId);
        $startDate = $this->getStartDate($range);

        return $startDate ? $query->where('created_at', '>=', $startDate)->count() : $query->count();
    }

    public function downloadPdf(Request $request)
    {
        $user = auth()->user();
        $userBranches = $this->getUserBranches($user);
        $branch_id = $request->branch_id;
        $data = [];

        if ($branch_id) {
            // Check if user has access to this branch
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                if (!$userBranchIds->contains($branch_id)) {
                    abort(403, 'Unauthorized access to this branch');
                }
            }

            $branch = Branch::with([
                'customers' => function ($query) {
                    $query->latest();
                }
            ])->findOrFail($branch_id);

            $data['branch'] = $branch;
            $data['customers'] = $branch->customers;
        } else {
            $period = $this->resolveDownloadReportPeriod($request);
            $periodStart = $period['start'];
            $periodEnd = $period['end'];
            $hasReportPeriod = $periodStart !== null && $periodEnd !== null;

            $data['hasReportPeriod'] = $hasReportPeriod;
            $data['reportPeriodLabel'] = $period['label'];

            // Apply branch filtering for non-Super Admin users
            if ($hasReportPeriod) {
                $branchQuery = Branch::withCount([
                    'customers as before_current_month_count' => function ($query) use ($periodStart) {
                        $query->where('created_at', '<', $periodStart);
                    },
                    'customers as current_month_count' => function ($query) use ($periodStart, $periodEnd) {
                        $query->where('created_at', '>=', $periodStart)
                            ->where('created_at', '<=', $periodEnd);
                    },
                    'customers as total_through_period_count' => function ($query) use ($periodEnd) {
                        $query->where('created_at', '<=', $periodEnd);
                    },
                ]);
            } else {
                $branchQuery = Branch::withCount('customers');
            }

            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                $branchQuery->whereIn('id', $userBranchIds);
            }

            $data['branches'] = $branchQuery->get();

            // Filter customers based on user's accessible branches
            $customerQuery = Customer::query();
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                $customerQuery->whereIn('branch_id', $userBranchIds);
            }

            $data['totalCustomers'] = $customerQuery->count();
        }

        $pdf = app('dompdf.wrapper');
        $this->configureDompdfBangla($pdf);

        // Load view
        $view = view('reports.pdf', $data)->render();

        // Load HTML to PDF
        $pdf->loadHTML($view);

        // Set paper
        $pdf->setPaper('A4', 'portrait');

        // Download
        return $pdf->download('block-list-report-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Shared DomPDF options + Bangla font (SolaimanLipi registered as family "bangla").
     */
    private function configureDompdfBangla($pdf): void
    {
        $config = [
            'fontDir' => storage_path('fonts/'),
            'fontCache' => storage_path('fonts/'),
            'defaultFont' => 'bangla',
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'screen',
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'dpi' => 96,
        ];

        $dompdf = $pdf->getDomPDF();

        foreach ($config as $key => $value) {
            $dompdf->set_option($key, $value);
        }

        if (!file_exists(storage_path('fonts'))) {
            mkdir(storage_path('fonts'), 0755, true);
        }

        $dompdf->getFontMetrics()->registerFont(
            [
                'family' => 'bangla',
                'style' => 'normal',
                'weight' => 'normal',
            ],
            public_path('fonts/SolaimanLipi.ttf')
        );
    }

    /**
     * Normalize user.role for branch user report columns (BM / RM / ZM / DMF).
     * Missing or blank role is treated as BM for reporting.
     */
    private function canonicalBranchReportRole(?string $role): ?string
    {
        if ($role === null || trim((string) $role) === '') {
            return 'BM';
        }

        $r = strtoupper(trim((string) $role));

        return in_array($r, ['BM', 'RM', 'ZM', 'DMF'], true) ? $r : null;
    }

    /**
     * Merge all users per role (multiple BMs etc.). Unknown roles count under BM with a label.
     *
     * @param  \Illuminate\Support\Collection<int, array{name: string, entries: int, role: ?string}>  $rows
     * @return array{0: array<string, array{total: int, lines: array<int, array<string, mixed>>}>, 1: int}
     */
    private function aggregateBranchUserRowsByRole($rows): array
    {
        $buckets = [
            'BM' => ['total' => 0, 'lines' => []],
            'RM' => ['total' => 0, 'lines' => []],
            'ZM' => ['total' => 0, 'lines' => []],
            'DMF' => ['total' => 0, 'lines' => []],
        ];

        foreach ($rows as $u) {
            $entries = (int) ($u['entries'] ?? 0);
            $canonical = $this->canonicalBranchReportRole($u['role'] ?? null);
            $key = $canonical ?? 'BM';
            $buckets[$key]['total'] += $entries;
            $line = ['name' => $u['name'], 'entries' => $entries];
            $rawTrim = trim((string) ($u['role'] ?? ''));
            if ($key === 'BM' && $rawTrim !== '') {
                $rUpper = strtoupper($rawTrim);
                if (! in_array($rUpper, ['BM', 'RM', 'ZM', 'DMF'], true)) {
                    $line['role_label'] = $rawTrim;
                }
            }
            $buckets[$key]['lines'][] = $line;
        }

        $total = $buckets['BM']['total'] + $buckets['RM']['total'] + $buckets['ZM']['total'] + $buckets['DMF']['total'];

        return [$buckets, $total];
    }

    public function branchUsersPdf(Request $request)
    {
        $user = auth()->user();
        $userBranches = $this->getUserBranches($user);

        $period = $this->resolveDownloadReportPeriod($request);
        $periodStart = $period['start'];
        $periodEnd = $period['end'];
        $hasReportPeriod = $periodStart !== null && $periodEnd !== null;

        $query = Branch::with(['users']);

        if ($request->branch_id) {
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                if (!$userBranchIds->contains($request->branch_id)) {
                    abort(403, 'Unauthorized access to this branch');
                }
            }
            $query->where('id', $request->branch_id);
        } else {
            $this->applyBranchFilter($query, $user, $userBranches, $request->input('branch_filter'));
        }

        $branches = $query->get()->map(function ($branch) use ($periodStart, $periodEnd, $hasReportPeriod) {
            $userEntriesQuery = Customer::where('branch_id', $branch->id)
                ->select('user_id', DB::raw('COUNT(*) as entry_count'));

            if ($hasReportPeriod) {
                $this->applyDateFilter($userEntriesQuery, $periodStart, $periodEnd);
            }

            $userRows = $userEntriesQuery->groupBy('user_id')
                ->with('user:id,name,role')
                ->get()
                ->filter(fn ($entry) => $entry->user !== null)
                ->map(function ($entry) {
                    return [
                        'user_id' => $entry->user->id,
                        'name' => $entry->user->name,
                        'entries' => (int) $entry->entry_count,
                        'role' => $entry->user->role,
                    ];
                })
                ->values();

            [$roleBuckets, $branchTotal] = $this->aggregateBranchUserRowsByRole($userRows);

            return [
                'name' => $branch->branch_name,
                'code' => $branch->branch_code,
                'users' => $userRows,
                'role_buckets' => $roleBuckets,
                'total' => $branchTotal,
            ];
        });

        $data = [
            'branches' => $branches,
            'reportPeriodLabel' => $period['label'],
        ];

        $pdf = app('dompdf.wrapper');
        $this->configureDompdfBangla($pdf);
        $pdf->loadView('reports.branch-users-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('branch-users-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
