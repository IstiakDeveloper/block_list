<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Create a block-list customer entry from an external system (e.g. MisLoan).
     * POST /api/customers
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blocked_by_username' => ['required', 'string', 'max:255'],
            'branch_code' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'name_bn' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'spouse_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['nullable', 'date', 'before:today'],
            'nid_number' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10,14}$/'],
            'address' => ['nullable', 'string', 'max:500'],
            'details' => ['nullable', 'string', 'max:1000'],
            'rejected_by' => ['nullable', 'string', 'max:255'],
        ], [
            'phone_number.regex' => 'Phone number must be between 10-14 digits.',
            'dob.before' => 'Date of birth must be a date before today.',
        ]);

        $blockedByUser = User::query()
            ->where('username', $validated['blocked_by_username'])
            ->first();

        if (! $blockedByUser) {
            return response()->json([
                'success' => false,
                'message' => 'Username "'.$validated['blocked_by_username'].'" block list সিস্টেমে পাওয়া যায়নি। দুই সিস্টেমে একই username থাকতে হবে।',
                'error_code' => 'username_not_found',
            ], 422);
        }

        $branch = Branch::findByFlexibleCode($validated['branch_code']);

        if (! $branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch code "'.$validated['branch_code'].'" block list সিস্টেমে পাওয়া যায়নি।',
                'error_code' => 'branch_not_found',
            ], 422);
        }

        if (! $blockedByUser->canAccessBranch($branch->id)) {
            return response()->json([
                'success' => false,
                'message' => 'এই শাখায় block list entry করার অনুমতি নেই।',
                'error_code' => 'branch_access_denied',
            ], 403);
        }

        // Idempotent: if this NID is already blocked, treat as success so the
        // source system (MisLoan) can safely retry without getting stuck.
        $existing = Customer::query()
            ->where('nid_number', $validated['nid_number'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'already_exists' => true,
                'message' => 'Customer already exists in block list.',
                'data' => [
                    'id' => $existing->id,
                    'user_id' => $existing->user_id,
                    'branch_id' => $existing->branch_id,
                    'nid_number' => $existing->nid_number,
                ],
            ], 200);
        }

        $customer = DB::transaction(function () use ($validated, $blockedByUser, $branch) {
            return Customer::create([
                'user_id' => $blockedByUser->id,
                'branch_id' => $branch->id,
                'name' => $validated['name'],
                'name_bn' => $validated['name_bn'] ?? null,
                'father_name' => $validated['father_name'] ?? null,
                'mother_name' => $validated['mother_name'] ?? null,
                'spouse_name' => $validated['spouse_name'] ?? null,
                'dob' => $validated['dob'] ?? null,
                'nid_number' => $validated['nid_number'],
                'phone_number' => $validated['phone_number'],
                'address' => $validated['address'] ?? null,
                'details' => $validated['details'] ?? null,
                'rejected_by' => $validated['rejected_by'] ?? null,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Customer added to block list successfully.',
            'data' => [
                'id' => $customer->id,
                'user_id' => $customer->user_id,
                'branch_id' => $customer->branch_id,
                'nid_number' => $customer->nid_number,
            ],
        ], 201);
    }
}
