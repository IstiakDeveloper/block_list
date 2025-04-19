<?php

namespace App\Http\Controllers\Admin\VoluntarySaving;

use App\Http\Controllers\Controller;
use App\Models\VoluntarySaving;
use App\Models\VoluntarySavingDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class BranchVoluntarySavingController extends Controller
{
    /**
     * Display a listing of the applications created by the branch.
     */
    public function index()
    {
        $branch_id = Auth::user()->branch_id;

        $voluntarySavings = VoluntarySaving::where('branch_id', $branch_id)
            ->with('deposits', 'branch')
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/VoluntarySaving/Branch/Index', [
            'voluntarySavings' => $voluntarySavings
        ]);
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        return Inertia::render('Admin/VoluntarySaving/Branch/Create');
    }

    /**
     * Store a newly created application in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'application_date' => 'required|date',
            'somiti_name' => 'required|string|max:255',
            'somiti_code' => 'required|string|max:255',
            'member_name' => 'required|string|max:255',
            'member_code' => 'required|string|max:255',
            'profit' => 'nullable|numeric',
            'member_mobile' => 'nullable|string|max:20',
            'applicant_name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'pin' => 'nullable|string|max:50',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deposits' => 'required|array|min:1',
            'deposits.*.deposit_date' => 'required|date',
            'deposits.*.deposit_amount' => 'required|numeric|min:0',
        ]);

        // Handle signature upload if present
        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        // Create voluntary saving application
        $voluntarySaving = VoluntarySaving::create([
            'application_date' => $request->application_date,
            'branch_id' => Auth::user()->branch_id,
            'somiti_name' => $request->somiti_name,
            'somiti_code' => $request->somiti_code,
            'member_name' => $request->member_name,
            'member_code' => $request->member_code,
            'profit' => $request->profit,
            'member_mobile' => $request->member_mobile,
            'applicant_name' => $request->applicant_name,
            'designation' => $request->designation,
            'pin' => $request->pin,
            'signature' => $signaturePath,
            'status' => 'pending',
        ]);

        // Add multiple deposits
        foreach ($request->deposits as $deposit) {
            VoluntarySavingDeposit::create([
                'voluntary_saving_id' => $voluntarySaving->id,
                'deposit_date' => $deposit['deposit_date'],
                'deposit_amount' => $deposit['deposit_amount'],
            ]);
        }

        return redirect()->route('branch.voluntary-savings.index')
            ->with('success', 'Voluntary Saving application created successfully.');
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits', 'branch')
            ->findOrFail($id);

        // Ensure the branch user can only see their own applications
        if (Auth::user()->branch_id != $voluntarySaving->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Admin/VoluntarySaving/Branch/Show', [
            'voluntarySaving' => $voluntarySaving
        ]);
    }

    /**
     * Show the form for editing the specified application.
     */
    public function edit($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits')
            ->findOrFail($id);

        // Ensure the branch user can only edit their own applications
        // and only if they are still pending
        if (Auth::user()->branch_id != $voluntarySaving->branch_id || $voluntarySaving->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Admin/VoluntarySaving/Branch/Edit', [
            'voluntarySaving' => $voluntarySaving
        ]);
    }

    /**
     * Update the specified application in storage.
     */
    public function update(Request $request, $id)
    {
        $voluntarySaving = VoluntarySaving::findOrFail($id);

        // Ensure the branch user can only update their own applications
        // and only if they are still pending
        if (Auth::user()->branch_id != $voluntarySaving->branch_id || $voluntarySaving->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'application_date' => 'required|date',
            'somiti_name' => 'required|string|max:255',
            'somiti_code' => 'required|string|max:255',
            'member_name' => 'required|string|max:255',
            'member_code' => 'required|string|max:255',
            'profit' => 'nullable|numeric',
            'member_mobile' => 'nullable|string|max:20',
            'applicant_name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'pin' => 'nullable|string|max:50',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'deposits' => 'required|array|min:1',
            'deposits.*.id' => 'nullable|exists:voluntary_saving_deposits,id',
            'deposits.*.deposit_date' => 'required|date',
            'deposits.*.deposit_amount' => 'required|numeric|min:0',
        ]);

        // Handle signature upload if present
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
            $voluntarySaving->signature = $signaturePath;
        }

        // Update voluntary saving application
        $voluntarySaving->update([
            'application_date' => $request->application_date,
            'somiti_name' => $request->somiti_name,
            'somiti_code' => $request->somiti_code,
            'member_name' => $request->member_name,
            'member_code' => $request->member_code,
            'profit' => $request->profit,
            'member_mobile' => $request->member_mobile,
            'applicant_name' => $request->applicant_name,
            'designation' => $request->designation,
            'pin' => $request->pin,
        ]);

        // Handle deposits - update existing, remove deleted, add new
        $existingDepositIds = [];

        foreach ($request->deposits as $depositData) {
            if (isset($depositData['id'])) {
                // Update existing deposit
                $deposit = VoluntarySavingDeposit::findOrFail($depositData['id']);
                $deposit->update([
                    'deposit_date' => $depositData['deposit_date'],
                    'deposit_amount' => $depositData['deposit_amount'],
                ]);
                $existingDepositIds[] = $deposit->id;
            } else {
                // Create new deposit
                $deposit = VoluntarySavingDeposit::create([
                    'voluntary_saving_id' => $voluntarySaving->id,
                    'deposit_date' => $depositData['deposit_date'],
                    'deposit_amount' => $depositData['deposit_amount'],
                ]);
                $existingDepositIds[] = $deposit->id;
            }
        }

        // Delete deposits that are not in the request
        $voluntarySaving->deposits()->whereNotIn('id', $existingDepositIds)->delete();

        return redirect()->route('branch.voluntary-savings.index')
            ->with('success', 'Voluntary Saving application updated successfully.');
    }

    /**
     * Generate PDF for the application.
     */
    public function generatePdf($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits', 'branch')
            ->findOrFail($id);

        // Ensure the branch user can only generate PDFs for their own applications
        if (Auth::user()->branch_id != $voluntarySaving->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $pdf = PDF::loadView('pdf.voluntary-saving-application', [
            'voluntarySaving' => $voluntarySaving
        ]);

        return $pdf->download('voluntary-saving-application-' . $voluntarySaving->id . '.pdf');
    }

    /**
     * Delete the specified application.
     */
    public function destroy($id)
    {
        $voluntarySaving = VoluntarySaving::findOrFail($id);

        // Ensure the branch user can only delete their own applications
        // and only if they are still pending
        if (Auth::user()->branch_id != $voluntarySaving->branch_id || $voluntarySaving->status !== 'pending') {
            abort(403, 'Unauthorized action.');
        }

        // Delete the application (deposits will be deleted by cascade)
        $voluntarySaving->delete();

        return redirect()->route('branch.voluntary-savings.index')
            ->with('success', 'Voluntary Saving application deleted successfully.');
    }
}
