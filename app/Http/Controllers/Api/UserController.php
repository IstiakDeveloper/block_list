<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Verify username exists in block_list (and optional branch access).
     * GET /api/users/verify?username=xxx&branch_code=yyy
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255'],
            'branch_code' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::query()
            ->where('username', $validated['username'])
            ->first();

        if (! $user) {
            return response()->json([
                'success' => true,
                'ok' => false,
                'found' => false,
                'has_branch_access' => false,
                'message' => 'Username "'.$validated['username'].'" block list সিস্টেমে পাওয়া যায়নি। দুই সিস্টেমে একই username থাকতে হবে।',
                'error_code' => 'username_not_found',
            ]);
        }

        $branchCode = trim((string) ($validated['branch_code'] ?? ''));
        if ($branchCode === '') {
            return response()->json([
                'success' => true,
                'ok' => true,
                'found' => true,
                'has_branch_access' => true,
                'message' => 'Username block list-এ পাওয়া গেছে।',
            ]);
        }

        $branch = Branch::findByFlexibleCode($branchCode);

        if (! $branch) {
            return response()->json([
                'success' => true,
                'ok' => false,
                'found' => true,
                'has_branch_access' => false,
                'message' => 'Branch code "'.$branchCode.'" block list সিস্টেমে পাওয়া যায়নি।',
                'error_code' => 'branch_not_found',
            ]);
        }

        if (! $user->canAccessBranch($branch->id)) {
            return response()->json([
                'success' => true,
                'ok' => false,
                'found' => true,
                'has_branch_access' => false,
                'message' => 'এই শাখায় block list entry করার অনুমতি নেই।',
                'error_code' => 'branch_access_denied',
            ]);
        }

        return response()->json([
            'success' => true,
            'ok' => true,
            'found' => true,
            'has_branch_access' => true,
            'message' => 'Username ও শাখা যাচাই সফল।',
        ]);
    }
}
