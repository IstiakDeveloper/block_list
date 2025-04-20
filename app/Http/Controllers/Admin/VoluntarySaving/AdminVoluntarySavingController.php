<?php

namespace App\Http\Controllers\Admin\VoluntarySaving;

use App\Http\Controllers\Controller;
use App\Models\VoluntarySaving;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminVoluntarySavingController extends Controller
{
    /**
     * Display a listing of all applications for super admin.
     */
    public function index(Request $request)
    {
        $query = VoluntarySaving::with('deposits', 'branch');

        // Filter by status if provided
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        // Filter by branch if provided
        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by date range if provided
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('application_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('application_date', '<=', $request->to_date);
        }

        $voluntarySavings = $query->latest()->paginate(10);

        // Get all branches for filter dropdown
        $branches = Branch::all();

        return Inertia::render('Admin/VoluntarySaving/Admin/Index', props: [
            'voluntarySavings' => $voluntarySavings,
            'branches' => $branches,
            'filters' => $request->only(['status', 'branch_id', 'from_date', 'to_date']),
        ]);
    }

    /**
     * Display the specified application.
     */
    public function show($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits', 'branch')
            ->findOrFail($id);

        return Inertia::render('Admin/VoluntarySaving/Admin/Show', [
            'voluntarySaving' => $voluntarySaving
        ]);
    }

    /**
     * Approve the specified application.
     */
    public function approve($id)
    {
        $voluntarySaving = VoluntarySaving::findOrFail($id);

        // Only pending applications can be approved
        if ($voluntarySaving->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be approved.');
        }

        $voluntarySaving->update([
            'status' => 'approved'
        ]);

        return redirect()->route('admin.voluntary-savings.index')
            ->with('success', 'Voluntary Saving application approved successfully.');
    }

    /**
     * Reject the specified application.
     */
    public function reject(Request $request, $id)
    {
        $voluntarySaving = VoluntarySaving::findOrFail($id);

        // Only pending applications can be rejected
        if ($voluntarySaving->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending applications can be rejected.');
        }

        $voluntarySaving->update([
            'status' => 'rejected'
        ]);

        return redirect()->route('admin.voluntary-savings.index')
            ->with('success', 'Voluntary Saving application rejected successfully.');
    }

    /**
     * Generate PDF for the application.
     */
    public function generatePdf($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits', 'branch')
            ->findOrFail($id);

        $pdf = PDF::loadView('pdf.voluntary-savings-withdrawal', [
            'voluntarySaving' => $voluntarySaving
        ]);

        return $pdf->download('voluntary-savings-withdrawal-' . $voluntarySaving->id . '.pdf');
    }

    /**
     * Generate PDF for the approval letter.
     */
    public function generateApprovalPdf($id)
    {
        $voluntarySaving = VoluntarySaving::with('deposits', 'branch')
            ->findOrFail($id);

        // Only approved applications can generate approval PDF
        if ($voluntarySaving->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved applications can generate approval letters.');
        }

        $pdf = PDF::loadView('pdf.voluntary-savings-withdrawal', [
            'voluntarySaving' => $voluntarySaving
        ]);

        return $pdf->download('voluntary-savings-withdrawal-' . $voluntarySaving->id . '.pdf');
    }

    /**
     * Generate PDF for all applications based on filters.
     */
    public function generateReportPdf(Request $request)
    {
        $query = VoluntarySaving::with('deposits', 'branch');

        // Apply filters
        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('branch_id') && $request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('application_date', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('application_date', '<=', $request->to_date);
        }

        $voluntarySavings = $query->latest()->get();

        $pdf = PDF::loadView('pdf.voluntary-saving-report', [
            'voluntarySavings' => $voluntarySavings,
            'filters' => $request->only(['status', 'branch_id', 'from_date', 'to_date']),
        ]);

        return $pdf->download('voluntary-saving-report.pdf');
    }
}
