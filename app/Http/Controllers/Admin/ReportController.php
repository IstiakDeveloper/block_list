<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{

    public function index()
    {
        $branchDetails = Branch::withCount('customers')
            ->with(['customers' => function($query) {
                $query->selectRaw('branch_id,
                    COUNT(*) as total,
                    COUNT(CASE WHEN created_at >= DATE_FORMAT(NOW(), "%Y-%m-01") THEN 1 END) as this_month,
                    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 END) as last_7_days')
                    ->groupBy('branch_id');
            }])
            ->get()
            ->map(function($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->branch_name,
                    'code' => $branch->branch_code,
                    'total_customers' => $branch->customers_count,
                    'this_month' => $branch->customers->first()->this_month ?? 0,
                    'last_7_days' => $branch->customers->first()->last_7_days ?? 0,
                ];
            });

        $reports = [
            'totalCustomers' => Customer::count(),
            'totalBranches' => Branch::count(),
            'branchWiseCustomers' => Branch::withCount('customers')->get(),
            'branchDetails' => $branchDetails,
            'recentCustomers' => Customer::with('branch')
                ->latest()
                ->take(5)
                ->get(),
            'monthlyCustomers' => Customer::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, count(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'ageDistribution' => Customer::selectRaw('
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 25 THEN "18-24"
                    WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 35 THEN "25-34"
                    WHEN TIMESTAMPDIFF(YEAR, dob, CURDATE()) < 45 THEN "35-44"
                    ELSE "45+"
                END as age_group,
                COUNT(*) as count
            ')
            ->whereNotNull('dob')
            ->groupBy('age_group')
            ->get(),
        ];

        return Inertia::render('Admin/Reports/Dashboard', [
            'reportData' => $reports
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $branch_id = $request->branch_id;
        $data = [];

        if ($branch_id) {
            $branch = Branch::with(['customers' => function($query) {
                $query->latest();
            }])->findOrFail($branch_id);

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
}
