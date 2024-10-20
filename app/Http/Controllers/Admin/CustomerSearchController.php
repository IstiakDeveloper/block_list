<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerSearchController extends Controller
{
/**
     * Handle the search request.
     */
    public function search(Request $request)
    {
        $criteria = $request->input('criteria');
        $query = $request->input('query');

        $customers = [];

        if ($criteria && $query) {
            $customers = Customer::where($criteria, 'LIKE', "%{$query}%")
                ->limit(10)
                ->get();
        }

        return Inertia::render('Admin/CustomerSearchResults', [
            'customers' => $customers
        ]);
    }


}
