<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use thiagoalessio\TesseractOCR\TesseractOCR;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Customer::with('branch');

        // If Super Admin and branch filter is applied
        if ($user->name === 'Super Admin' && $request->has('branch')) {
            $query->where('branch_id', $request->branch);
        }
        // If not Super Admin, only show customers from their branch
        elseif ($user->name !== 'Super Admin') {
            $query->where('branch_id', $user->branch_id);
        }

        $customers = $query->latest()->paginate(10);
        $branches = Branch::all(); // Get all branches for the filter dropdown

        return Inertia::render('Admin/Customer/Index', [
            'customers' => $customers,
            'branches' => $branches,
        ]);
    }

        // Display the form for creating a new customer

    public function create()
    {
        return Inertia::render('Admin/Customer/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nid_part_1' => 'nullable|file|image|max:2048',
            'nid_part_2' => 'nullable|file|image|max:2048',
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'rejected_by' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'nid_number' => 'nullable|string|unique:customers',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'details' => 'nullable|string',
        ]);

        // Initialize file paths
        $nid_part_1_path = $request->file('nid_part_1') ? $request->file('nid_part_1')->store('nid_images', 'public') : null;
        $nid_part_2_path = $request->file('nid_part_2') ? $request->file('nid_part_2')->store('nid_images', 'public') : null;

        // Attempt to extract information from images only if they exist
        $extracted_info = [];
        if ($nid_part_1_path || $nid_part_2_path) {
            $extracted_info = $this->extractInfoFromImages($nid_part_1_path, $nid_part_2_path);
        }

        // Create new customer
        $customer = new Customer($validated);
        $customer->user_id = auth()->id();
        $customer->branch_id = auth()->user()->branch_id;
        $customer->nid_part_1 = $nid_part_1_path;
        $customer->nid_part_2 = $nid_part_2_path;

        // Use extracted info if available, otherwise use manually entered data
        $customer->name = $extracted_info['name'] ?? $validated['name'];
        $customer->nid_number = $extracted_info['nid_number'] ?? $validated['nid_number'];
        $customer->phone_number = $validated['phone_number'];

        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
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
        // Retrieve the customer by ID
        $customer = Customer::findOrFail($id);

        // Return the Inertia view (Edit.vue) with the customer data
        return Inertia::render('Admin/Customer/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_bn' => 'nullable|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'rejected_by' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'nid_number' => 'required|string|unique:customers,nid_number,' . $customer->id,
            'address' => 'nullable|string',
            'details' => 'nullable|string',
        ]);

        // Update customer data
        $customer->update($validated);

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
