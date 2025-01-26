<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
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

        $customers = $query->latest()->paginate(10);

        return Inertia::render('Admin/Customer/Index', [
            'customers' => $customers,
            'branches' => $userBranches,
            'filters' => [
                'branch' => $request->branch,
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

    // Remove the specified customer from storage
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully!');
    }
}
