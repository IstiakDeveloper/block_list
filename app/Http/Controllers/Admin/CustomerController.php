<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use thiagoalessio\TesseractOCR\TesseractOCR;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Customer::with('branch');

        // Get user's branches
        $userBranches = match (true) {
            $user->name === 'Super Admin' => Branch::all(),
            $user->branches()->exists() => $user->branches,
            default => Branch::where('id', $user->branch_id)->get()
        };

        // If user has only one branch and no branch filter is set, automatically set it
        if ($userBranches->count() === 1 && !$request->has('branch')) {
            $request->merge(['branch' => $userBranches->first()->id]);
        }

        // Create base query for total count (before any filters)
        $totalQuery = Customer::query();
        if ($user->name !== 'Super Admin') {
            $userBranchIds = $userBranches->pluck('id');
            $totalQuery->whereIn('branch_id', $userBranchIds);
        }
        $totalCustomers = $totalQuery->count();

        // Apply branch filtering
        if ($request->has('branch') && $request->branch) {
            // For Super Admin, allow filtering by any branch
            if ($user->name === 'Super Admin') {
                $query->where('branch_id', $request->branch);
            }
            // For other users, only allow filtering by their assigned branches
            else {
                $userBranchIds = $userBranches->pluck('id');
                if ($userBranchIds->contains($request->branch)) {
                    $query->where('branch_id', $request->branch);
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

        // Apply search filtering
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('name_bn', 'like', "%{$searchTerm}%")
                    ->orWhere('nid_number', 'like', "%{$searchTerm}%")
                    ->orWhere('phone_number', 'like', "%{$searchTerm}%")
                    ->orWhere('father_name', 'like', "%{$searchTerm}%")
                    ->orWhere('mother_name', 'like', "%{$searchTerm}%");
            });
        }

        // Apply date filtering
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $customers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Customer/Index', [
            'customers' => $customers,
            'branches' => $userBranches,
            'totalCustomers' => $totalCustomers,
            'filters' => [
                'branch' => $request->branch,
                'search' => $request->search,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
        ]);
    }

    // Display the form for creating a new customer

    public function create()
    {
        // For single branch user
        if (auth()->user()->branch_id) {
            $branches = [Branch::find(auth()->user()->branch_id)];
        }
        // For multiple branch user
        else {
            $branches = auth()->user()->branches;
        }

        return Inertia::render('Admin/Customer/Create', [
            'branches' => $branches,
        ]);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'nid_part_1' => 'nullable|image|max:2048',
            'nid_part_2' => 'nullable|image|max:2048',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'spouse_name' => 'nullable|string|max:255',
            'dob' => 'nullable|date|before:today',
            'nid_number' => 'nullable|string|unique:customers,nid_number',
            'phone_number' => 'nullable|string|regex:/^[0-9]{10,14}$/',
            'address' => 'nullable|string|max:500',
            'details' => 'nullable|string|max:1000'
        ], [
            'phone_number.regex' => 'Phone number must be between 10-14 digits.',
            'dob.before' => 'Date of birth must be a date before today.',
            'nid_number.unique' => 'This NID number is already registered.'
        ]);

        DB::transaction(function () use ($request, $validated) {
            // File upload handling
            $nid_part_1_path = $request->file('nid_part_1')
                ? $request->file('nid_part_1')->store('nid_images', 'public')
                : null;

            $nid_part_2_path = $request->file('nid_part_2')
                ? $request->file('nid_part_2')->store('nid_images', 'public')
                : null;

            // Create customer
            $customer = new Customer($validated);
            $customer->user_id = auth()->id();
            $customer->nid_part_1 = $nid_part_1_path;
            $customer->nid_part_2 = $nid_part_2_path;
            $customer->save();
        });

        return to_route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }


    private function extractInfoFromImages($part1_path, $part2_path)
    {
        $extracted_info = [
            'name' => null,
            'nid_number' => null,
        ];

        try {
            $ocr = new TesseractOCR();

            // Process part 1
            $text_part1 = $ocr->image(Storage::disk('public')->path($part1_path))->run();

            // Process part 2
            $text_part2 = $ocr->image(Storage::disk('public')->path($part2_path))->run();

            // Extract name and NID number (this is a simplified example and may need to be adjusted)
            if (preg_match('/Name:\s*(.+)/i', $text_part1 . $text_part2, $matches)) {
                $extracted_info['name'] = trim($matches[1]);
            }

            if (preg_match('/NID No:\s*(\d+)/i', $text_part1 . $text_part2, $matches)) {
                $extracted_info['nid_number'] = trim($matches[1]);
            }
        } catch (\Exception $e) {
            // Log the error, but continue with the process
            \Log::error('Error in OCR processing: ' . $e->getMessage());
        }

        return $extracted_info;
    }

    // Show the form for editing the specified customer
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $branches = Branch::whereHas('users', function ($query) {
            $query->where('users.id', auth()->id());
        })->get();

        return Inertia::render('Admin/Customer/Edit', [
            'customer' => $customer,
            'branches' => $branches,
        ]);
    }
    public function update(Request $request, Customer $customer)
    {
        $data = $request->except(['nid_part_1', 'nid_part_2']);

        // Handle file uploads
        if ($request->hasFile('nid_part_1')) {
            $data['nid_part_1'] = $request->file('nid_part_1')->store('nid_images', 'public');
        }

        if ($request->hasFile('nid_part_2')) {
            $data['nid_part_2'] = $request->file('nid_part_2')->store('nid_images', 'public');
        }

        // Update the customer with all the data from the request
        $customer->update($data);

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function show($id)
    {
        // Eager load 'branch' and 'user' relationships
        $customer = Customer::with(['branch', 'user'])->findOrFail($id);

        return Inertia::render('Admin/Customer/Show', [
            'customer' => $customer,
        ]);
    }

    public function downloadPdf($id)
    {
        // Eager load 'branch' and 'user' relationships
        $customer = Customer::with(['branch', 'user'])->findOrFail($id);

        // Create a new mPDF instance with proper Bangla font configuration
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 25,
            'margin_right' => 25,
            'margin_top' => 25,
            'margin_bottom' => 25,
            'fontDir' => array_merge($fontDirs, [
                public_path('fonts'),
            ]),
            'fontdata' => array_merge($fontData, [
                'kalpurush' => [
                    'R' => 'kalpurush.ttf',
                    'useOTL' => 0xFF,    // Use OpenType Layout features
                    'useKashida' => 75,  // Use kashida for justification
                ],
                'nikosh' => [
                    'R' => 'nikosh.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
                'sutonnymj' => [
                    'R' => 'SutonnyMJ.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ]),
            'default_font' => 'kalpurush',
            'tempDir' => storage_path('app/pdf-temp'),
            'debug' => true, // Enable debugging if needed
            'allow_charset_conversion' => true,
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
        ]);

        // Set document information
        $mpdf->SetTitle('Customer Profile - ' . $customer->name);
        $mpdf->SetAuthor('Mousumi NGO');
        $mpdf->SetCreator('Mousumi NGO');

        // Generate PDF content from view
        $html = view('pdfs.customer-profile-mpdf', ['customer' => $customer])->render();

        // Load the HTML into mPDF
        $mpdf->WriteHTML($html);

        // Output the PDF as a download
        return $mpdf->Output("customer-profile-{$customer->id}.pdf", \Mpdf\Output\Destination::DOWNLOAD);
    }

    // Remove the specified customer from storage
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully!');
    }
}
