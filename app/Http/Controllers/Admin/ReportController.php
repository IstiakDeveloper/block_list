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
        return match (true) {
            $user->name === 'Super Admin' => Branch::all(),
            $user->branches()->exists() => $user->branches,
            default => Branch::where('id', $user->branch_id)->get()
        };
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
            // Apply branch filtering for non-Super Admin users
            $branchQuery = Branch::withCount('customers')->with('customers');

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

            $monthlyTrendQuery = Customer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12);

            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                $monthlyTrendQuery->whereIn('branch_id', $userBranchIds);
            }

            $data['monthlyTrend'] = $monthlyTrendQuery->get();
        }

        // Create the PDF instance with specific configuration
        $pdf = app('dompdf.wrapper');

        // Configure DOMPDF
        $config = array(
            'fontDir' => storage_path('fonts/'), // directory
            'fontCache' => storage_path('fonts/'), // directory
            'defaultFont' => 'bangla',
            'isRemoteEnabled' => true,
            'isPhpEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isFontSubsettingEnabled' => true,
            'defaultMediaType' => 'screen',
            'defaultPaperSize' => 'a4',
            'defaultPaperOrientation' => 'portrait',
            'dpi' => 96,
        );

        // Get the DomPDF instance
        $dompdf = $pdf->getDomPDF();

        // Set options
        foreach ($config as $key => $value) {
            $dompdf->set_option($key, $value);
        }

        // Ensure font directories exist
        if (!file_exists(storage_path('fonts'))) {
            mkdir(storage_path('fonts'), 0755, true);
        }

        // Font paths
        $fontFile = public_path('fonts/SolaimanLipi.ttf');
        $fontFamily = 'bangla';

        // Register the font
        $dompdf->getFontMetrics()->registerFont(
            [
                'family' => $fontFamily,
                'style' => 'normal',
                'weight' => 'normal'
            ],
            $fontFile
        );

        // Load view
        $view = view('reports.pdf', $data)->render();

        // Load HTML to PDF
        $pdf->loadHTML($view);

        // Set paper
        $pdf->setPaper('A4', 'portrait');

        // Download
        return $pdf->download('block-list-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function branchUsersPdf(Request $request)
    {
        $user = auth()->user();
        $userBranches = $this->getUserBranches($user);

        $dateRange = $request->dateRange ?? 'all';
        $startDate = $this->getStartDate($dateRange);
        $endDate = $request->endDate ? Carbon::parse($request->endDate)->endOfDay() : null;

        if ($dateRange === 'custom') {
            $startDate = $request->startDate ? Carbon::parse($request->startDate)->startOfDay() : null;
        }

        $query = Branch::with(['users']);

        // Apply branch filtering based on user permissions
        if ($request->branch_id) {
            // Check if user has access to this specific branch
            if ($user->name !== 'Super Admin') {
                $userBranchIds = $userBranches->pluck('id');
                if (!$userBranchIds->contains($request->branch_id)) {
                    abort(403, 'Unauthorized access to this branch');
                }
            }
            $query->where('id', $request->branch_id);
        } else {
            // Apply general branch filtering
            $this->applyBranchFilter($query, $user, $userBranches);
        }

        $branches = $query->get()->map(function ($branch) use ($startDate, $endDate) {
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
                'name' => $branch->branch_name,
                'code' => $branch->branch_code,
                'users' => $userEntries,
                'total' => $userEntries->sum('entries')
            ];
        });

        $data = [
            'branches' => $branches,
            'dateRange' => $dateRange,
            'startDate' => $startDate ? $startDate->format('Y-m-d') : null,
            'endDate' => $endDate ? $endDate->format('Y-m-d') : null,
        ];

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('reports.branch-users-pdf', $data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->download('branch-users-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
