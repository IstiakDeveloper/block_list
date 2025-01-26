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

    public function index()
    {
        $dateRange = request('dateRange', 'all');
        $startDate = null;
        $endDate = null;

        if ($dateRange === 'custom') {
            $startDate = request('startDate') ? Carbon::parse(request('startDate'))->startOfDay() : null;
            $endDate = request('endDate') ? Carbon::parse(request('endDate'))->endOfDay() : null;
        } else {
            $startDate = $this->getStartDate($dateRange);
        }

        $branchDetails = Branch::with(['users'])
            ->withCount([
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
                    ->with('user:id,name')
                    ->get()
                    ->map(function ($entry) {
                        return [
                            'name' => $entry->user->name,
                            'entries' => $entry->entry_count
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
            'startDate' => request('startDate'),
            'endDate' => request('endDate')
        ];

        return Inertia::render('Admin/Reports/Dashboard', [
            'reportData' => array_merge(
                $this->getReportData($branchDetails, $dateRange, $startDate, $endDate),
                ['filter' => $filterData]
            )
        ]);
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

    private function getReportData($branchDetails, $dateRange, $startDate, $endDate)
    {
        $customerQuery = Customer::query();
        $this->applyDateFilter($customerQuery, $startDate, $endDate);

        return [
            'totalCustomers' => $customerQuery->count(),
            'totalBranches' => Branch::count(),
            'branchWiseCustomers' => Branch::withCount([
                'customers' => function ($query) use ($startDate, $endDate) {
                    $this->applyDateFilter($query, $startDate, $endDate);
                }
            ])->get(),
            'branchDetails' => $branchDetails,
            'recentCustomers' => Customer::with(['branch', 'user'])->latest()->take(5)->get(),
            'monthlyCustomers' => $this->getMonthlyData($startDate, $endDate),
            'ageDistribution' => $this->getAgeDistribution($startDate, $endDate)
        ];
    }

    private function getMonthlyData($startDate, $endDate)
    {
        $query = Customer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
            ->groupBy('month')
            ->orderBy('month');

        $this->applyDateFilter($query, $startDate, $endDate);

        return $query->get();
    }

    private function getAgeDistribution($startDate, $endDate)
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
        $branch_id = $request->branch_id;
        $data = [];

        if ($branch_id) {
            $branch = Branch::with([
                'customers' => function ($query) {
                    $query->latest();
                }
            ])->findOrFail($branch_id);

            $data['branch'] = $branch;
            $data['customers'] = $branch->customers;
        } else {
            $data['branches'] = Branch::withCount('customers')
                ->with('customers')
                ->get();

            $data['totalCustomers'] = Customer::count();

            $data['monthlyTrend'] = Customer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month', 'desc')
                ->take(12)
                ->get();
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
        $dateRange = $request->dateRange ?? 'all';
        $startDate = $this->getStartDate($dateRange);
        $endDate = $request->endDate ? Carbon::parse($request->endDate)->endOfDay() : null;

        if ($dateRange === 'custom') {
            $startDate = $request->startDate ? Carbon::parse($request->startDate)->startOfDay() : null;
        }

        $query = Branch::with(['users']);

        if ($request->branch_id) {
            $query->where('id', $request->branch_id);
        }

        $branches = $query->get()->map(function ($branch) use ($startDate, $endDate) {
            $userEntriesQuery = Customer::where('branch_id', $branch->id)
                ->select('user_id', DB::raw('COUNT(*) as entry_count'));

            $this->applyDateFilter($userEntriesQuery, $startDate, $endDate);

            $userEntries = $userEntriesQuery->groupBy('user_id')
                ->with('user:id,name')
                ->get()
                ->map(function ($entry) {
                    return [
                        'name' => $entry->user->name,
                        'entries' => $entry->entry_count
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
