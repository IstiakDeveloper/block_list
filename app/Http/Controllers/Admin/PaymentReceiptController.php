<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\HeadOfficeInventory;
use App\Models\Lot;
use App\Models\ReceiptBook;
use App\Models\StockTransaction;
use App\Models\BookDistribution;
use Illuminate\Http\Request;
use App\Models\PaymentReceipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentReceiptController extends Controller
{
    /**
     * Main Dashboard - Branch or Super Admin
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSuperAdmin = $user->name === "Super Admin";

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        if ($isSuperAdmin) {
            return $this->superAdminDashboard($request);
        } else {
            return $this->branchUserDashboard($request);
        }
    }

    /**
     * Super Admin Dashboard
     */
    private function superAdminDashboard(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $selectedBranch = $request->input('branch_id');

        // Get all branches
        $branches = Branch::orderBy('branch_name')->get();

        // Get active lots with available books count
        $activeLots = Lot::where('is_active', true)
            ->withCount([
                'receiptBooks as available_books_count' => function ($q) {
                    $q->where('status', 'available')
                        ->where('location_type', 'head_office');
                }
            ])
            ->with(['receiptBooks' => function ($q) {
                $q->where('status', 'available')
                    ->where('location_type', 'head_office')
                    ->orderBy('book_number')
                    ->limit(10);
            }])
            ->get()
            ->map(function ($lot) {
                $books = $lot->receiptBooks;
                return [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'lot_name' => $lot->lot_name,
                    'available_books_count' => $lot->available_books_count,
                    'book_range' => $books->isNotEmpty()
                        ? $books->first()->book_number . ' - ' . $books->last()->book_number
                        : 'No books available',
                    'total_receipts' => $books->sum(fn($b) => $b->getTotalReceipts())
                ];
            });

        // Calculate current head office stock
        $currentStock = ReceiptBook::where('status', 'available')
            ->where('location_type', 'head_office')
            ->sum(DB::raw('(to_number - from_number + 1)'));

        // Get branch summaries
        $branchSummaries = Branch::select('branches.*')
            ->when($selectedBranch, fn($q) => $q->where('branches.id', $selectedBranch))
            ->get()
            ->map(function ($branch) use ($startDate, $endDate) {
                // Period data
                $periodReceived = StockTransaction::where('branch_id', $branch->id)
                    ->where('transaction_type', 'distribute_to_branch')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('total_receipts');

                $periodDistributed = StockTransaction::where('branch_id', $branch->id)
                    ->where('transaction_type', 'distribute_to_person')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('total_receipts');

                // All time data
                $allTimeReceived = StockTransaction::where('branch_id', $branch->id)
                    ->where('transaction_type', 'distribute_to_branch')
                    ->sum('total_receipts');

                $allTimeDistributed = StockTransaction::where('branch_id', $branch->id)
                    ->where('transaction_type', 'distribute_to_person')
                    ->sum('total_receipts');

                // Current available
                $currentAvailable = ReceiptBook::where('location_type', 'branch')
                    ->where('location_id', $branch->id)
                    ->where('status', 'distributed')
                    ->get()
                    ->sum(fn($b) => $b->getTotalReceipts());

                return [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->branch_name,
                    'branch_code' => $branch->branch_code,
                    'period_received' => $periodReceived,
                    'period_distributed' => $periodDistributed,
                    'all_time_received' => $allTimeReceived,
                    'all_time_distributed' => $allTimeDistributed,
                    'current_available' => $currentAvailable
                ];
            });

        // Get transactions
        $query = StockTransaction::with(['branch', 'lot'])
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($selectedBranch) {
            $query->where('branch_id', $selectedBranch);
        }

        $receipts = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('PaymentReceipts/SuperAdminIndex', [
            'receipts' => $receipts,
            'branchSummaries' => $branchSummaries,
            'branches' => $branches,
            'activeLots' => $activeLots,
            'currentStock' => $currentStock,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'branch_id' => $selectedBranch,
            ],
        ]);
    }

    /**
     * Branch User Dashboard
     */
    private function branchUserDashboard(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $branchId = auth()->user()->branch_id;

        // Get available books for this branch grouped by lot
        $availableBooks = ReceiptBook::where('location_type', 'branch')
            ->where('location_id', $branchId)
            ->where('status', 'distributed')
            ->with('lot')
            ->orderBy('lot_id')
            ->orderBy('book_number')
            ->get()
            ->groupBy('lot.lot_number')
            ->map(function ($lotBooks, $lotNumber) {
                return [
                    'lot_number' => $lotNumber,
                    'lot_id' => $lotBooks->first()->lot_id,
                    'total_books' => $lotBooks->count(),
                    'total_receipts' => $lotBooks->sum(fn($b) => $b->getTotalReceipts()),
                    'book_range' => $lotBooks->first()->book_number . ' - ' . $lotBooks->last()->book_number,
                    'receipt_range' => $lotBooks->first()->from_number . ' - ' . $lotBooks->last()->to_number,
                    'books' => $lotBooks->map(fn($b) => [
                        'book_number' => $b->book_number,
                        'from_number' => $b->from_number,
                        'to_number' => $b->to_number,
                    ])
                ];
            })->values();

        // Calculate summary
        $periodReceived = StockTransaction::where('branch_id', $branchId)
            ->where('transaction_type', 'distribute_to_branch')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_receipts');

        $periodDistributed = StockTransaction::where('branch_id', $branchId)
            ->where('transaction_type', 'distribute_to_person')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_receipts');

        $allTimeReceived = StockTransaction::where('branch_id', $branchId)
            ->where('transaction_type', 'distribute_to_branch')
            ->sum('total_receipts');

        $allTimeDistributed = StockTransaction::where('branch_id', $branchId)
            ->where('transaction_type', 'distribute_to_person')
            ->sum('total_receipts');

        $currentAvailable = $availableBooks->sum('total_receipts');

        $branchSummaries = (object) [
            'period_received' => $periodReceived,
            'period_distributed' => $periodDistributed,
            'all_time_received' => $allTimeReceived,
            'all_time_distributed' => $allTimeDistributed,
            'current_available' => $currentAvailable
        ];

        // Get transactions
        $receipts = StockTransaction::with(['lot', 'branch'])
            ->where('branch_id', $branchId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('PaymentReceipts/Index', [
            'receipts' => $receipts,
            'branchSummaries' => $branchSummaries,
            'availableBooks' => $availableBooks,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'isSuperAdmin' => false
        ]);
    }

    /**
     * Stock Add - Super Admin adds stock with Lot and Book Numbers
     * Entry: Lot selection + Book Number Range
     * Auto Generate: Receipt Numbers
     */
    public function stockIn(Request $request)
    {
        $validated = $request->validate([
            'lot_option' => 'required|in:existing,new',
            'lot_id' => 'required_if:lot_option,existing|nullable|exists:lots,id',
            'lot_name' => 'required_if:lot_option,new|nullable|string|max:255',
            'book_from' => 'required|integer|min:1',
            'book_to' => 'required|integer|gte:book_from',
        ]);

        try {
            DB::beginTransaction();

            // Create or get lot
            if ($request->lot_option === 'new') {
                $lot = Lot::create([
                    'lot_number' => Lot::generateNextLotNumber(),
                    'lot_name' => $request->lot_name,
                    'is_active' => true
                ]);
            } else {
                $lot = Lot::findOrFail($request->lot_id);
            }

            $bookFrom = (int) $request->book_from;
            $bookTo = (int) $request->book_to;
            $totalBooks = $bookTo - $bookFrom + 1;

            // Check if books already exist in this lot
            $existingBooks = ReceiptBook::where('lot_id', $lot->id)
                ->whereBetween('book_number', [$bookFrom, $bookTo])
                ->exists();

            if ($existingBooks) {
                throw new \Exception('Some books in this range already exist in the selected lot');
            }

            // Create individual books
            $createdBooks = [];
            for ($bookNum = $bookFrom; $bookNum <= $bookTo; $bookNum++) {
                // Fixed formula: Book 1 → 1-100, Book 665 → 66401-66500
                $fromReceipt = (($bookNum - 1) * 100) + 1;
                $toReceipt = $bookNum * 100;

                $book = ReceiptBook::create([
                    'lot_id' => $lot->id,
                    'book_number' => $bookNum,
                    'from_number' => $fromReceipt,
                    'to_number' => $toReceipt,
                    'status' => 'available',
                    'location_type' => 'head_office',
                    'location_id' => null,
                ]);

                $createdBooks[] = $book;
            }

            // Calculate receipt numbers - Fixed formula
            $receiptFrom = (($bookFrom - 1) * 100) + 1;
            $receiptTo = $bookTo * 100;
            $totalReceipts = $totalBooks * 100;

            // Create stock transaction
            $transaction = StockTransaction::create([
                'lot_id' => $lot->id,
                'transaction_type' => 'stock_in',
                'transaction_date' => now(),
                'book_from' => $bookFrom,
                'book_to' => $bookTo,
                'receipt_from' => $receiptFrom,
                'receipt_to' => $receiptTo,
                'total_books' => $totalBooks,
                'total_receipts' => $totalReceipts,
                'received_by' => auth()->user()->name,
                'remarks' => 'Stock added to head office'
            ]);

            // Link books to transaction
            foreach ($createdBooks as $book) {
                BookDistribution::create([
                    'stock_transaction_id' => $transaction->id,
                    'receipt_book_id' => $book->id
                ]);
            }

            // Update or create head office inventory
            $inventory = HeadOfficeInventory::firstOrCreate(
                ['lot_id' => $lot->id],
                [
                    'total_books' => 0,
                    'total_stock' => 0,
                    'total_stock_in' => 0,
                    'total_stock_out' => 0
                ]
            );

            $inventory->total_books += $totalBooks;
            $inventory->total_stock_in += $totalReceipts;
            $inventory->total_stock = $inventory->total_stock_in - $inventory->total_stock_out;
            $inventory->save();

            DB::commit();

            Log::info('Stock added successfully', [
                'lot' => $lot->lot_number,
                'books' => "{$bookFrom}-{$bookTo}",
                'receipts' => $totalReceipts
            ]);

            return back()->with('success', "Stock added: {$totalBooks} books (Book #{$bookFrom}-#{$bookTo}) = {$totalReceipts} receipts to {$lot->lot_number}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stock add failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to add stock: ' . $e->getMessage());
        }
    }

    /**
     * Super Admin Distributes to Branch
     * Entry: Branch + Lot + Book Range
     * Auto Generate: Receipt Numbers
     * Validation: Books must be available
     */
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'lot_id' => 'required|exists:lots,id',
            'book_from' => 'required|integer|min:1',
            'book_to' => 'required|integer|gte:book_from',
            'transaction_date' => 'nullable|date',
            'received_by' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $lotId = (int) $request->lot_id;
            $branchId = (int) $request->branch_id;
            $bookFrom = (int) $request->book_from;
            $bookTo = (int) $request->book_to;
            $transactionDate = $request->transaction_date ?? now();

            // Get branch details
            $branch = Branch::findOrFail($branchId);

            // Check if books are available at head office
            $availableBooks = ReceiptBook::where('lot_id', $lotId)
                ->where('status', 'available')
                ->where('location_type', 'head_office')
                ->whereBetween('book_number', [$bookFrom, $bookTo])
                ->orderBy('book_number')
                ->get();

            $expectedCount = $bookTo - $bookFrom + 1;

            if ($availableBooks->count() !== $expectedCount) {
                $missingBooks = [];
                for ($i = $bookFrom; $i <= $bookTo; $i++) {
                    if (!$availableBooks->where('book_number', $i)->count()) {
                        $missingBooks[] = $i;
                    }
                }
                throw new \Exception("Books not available. Missing: " . implode(', ', $missingBooks));
            }

            $totalBooks = $availableBooks->count();
            // Fixed formula: Book 1 → 1-100, Book 665 → 66401-66500
            $receiptFrom = (($bookFrom - 1) * 100) + 1;
            $receiptTo = $bookTo * 100;
            $totalReceipts = $totalBooks * 100;

            // Create stock transaction
            $transaction = StockTransaction::create([
                'lot_id' => $lotId,
                'transaction_type' => 'distribute_to_branch',
                'branch_id' => $branchId,
                'transaction_date' => $transactionDate,
                'book_from' => $bookFrom,
                'book_to' => $bookTo,
                'receipt_from' => $receiptFrom,
                'receipt_to' => $receiptTo,
                'total_books' => $totalBooks,
                'total_receipts' => $totalReceipts,
                'received_by' => $request->received_by ?? $branch->branch_name,
                'remarks' => "Distributed to {$branch->branch_name}"
            ]);

            // Update books location and status
            foreach ($availableBooks as $book) {
                $book->update([
                    'status' => 'distributed',
                    'location_type' => 'branch',
                    'location_id' => $branchId,
                    'parent_transaction_id' => $transaction->id
                ]);

                // Link book to transaction
                BookDistribution::create([
                    'stock_transaction_id' => $transaction->id,
                    'receipt_book_id' => $book->id
                ]);
            }

            // Update head office inventory
            $inventory = HeadOfficeInventory::where('lot_id', $lotId)->first();
            if ($inventory) {
                $inventory->total_stock_out += $totalReceipts;
                $inventory->total_stock = $inventory->total_stock_in - $inventory->total_stock_out;
                $inventory->save();
            }

            DB::commit();

            Log::info('Books distributed to branch', [
                'branch' => $branch->branch_name,
                'books' => "{$bookFrom}-{$bookTo}",
                'receipts' => $totalReceipts
            ]);

            return back()->with('success', "Distributed {$totalBooks} books (Book #{$bookFrom}-#{$bookTo}) = {$totalReceipts} receipts to {$branch->branch_name}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Branch distribution failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to distribute: ' . $e->getMessage());
        }
    }

    /**
     * Branch Distributes to Person
     * Entry: Person name + PIN + Quantity
     * Auto Select: Next available books from branch stock
     * Auto Generate: Book numbers, Receipt numbers
     * Validation: Enough stock available
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'given_to' => 'required|string|max:255',
            'pin_number' => 'nullable|string|max:50',
            'quantity' => 'required|integer|min:100',
            'transaction_date' => 'nullable|date',
        ]);

        try {
            DB::beginTransaction();

            $branchId = auth()->user()->branch_id;
            $quantity = (int) $request->quantity;
            $transactionDate = $request->transaction_date ?? now();

            // Validate quantity is multiple of 100
            if ($quantity % 100 !== 0) {
                throw new \Exception('Quantity must be in multiples of 100 (each book contains 100 receipts)');
            }

            $booksNeeded = $quantity / 100;

            // Get available books at branch (auto select from start)
            $availableBooks = ReceiptBook::where('location_type', 'branch')
                ->where('location_id', $branchId)
                ->where('status', 'distributed')
                ->orderBy('lot_id')
                ->orderBy('book_number')
                ->limit($booksNeeded)
                ->get();

            // Check if enough books available
            if ($availableBooks->count() < $booksNeeded) {
                $currentStock = ReceiptBook::where('location_type', 'branch')
                    ->where('location_id', $branchId)
                    ->where('status', 'distributed')
                    ->get()
                    ->sum(fn($b) => $b->getTotalReceipts());

                throw new \Exception("Not enough stock! You need {$booksNeeded} books ({$quantity} receipts), but only " . ($availableBooks->count()) . " books ({$currentStock} receipts) available");
            }

            // Get transaction details (use actual book data, no calculation needed)
            $lotId = $availableBooks->first()->lot_id;
            $bookFrom = $availableBooks->first()->book_number;
            $bookTo = $availableBooks->last()->book_number;
            $receiptFrom = $availableBooks->first()->from_number; // Already correct from DB
            $receiptTo = $availableBooks->last()->to_number;       // Already correct from DB

            // Create stock transaction
            $transaction = StockTransaction::create([
                'lot_id' => $lotId,
                'transaction_type' => 'distribute_to_person',
                'branch_id' => $branchId,
                'transaction_date' => $transactionDate,
                'book_from' => $bookFrom,
                'book_to' => $bookTo,
                'receipt_from' => $receiptFrom,
                'receipt_to' => $receiptTo,
                'total_books' => $booksNeeded,
                'total_receipts' => $quantity,
                'given_to' => $request->given_to,
                'pin_number' => $request->pin_number,
                'remarks' => "Distributed to {$request->given_to}" . ($request->pin_number ? " (PIN: {$request->pin_number})" : '')
            ]);

            // Update books status
            foreach ($availableBooks as $book) {
                $book->update([
                    'status' => 'used',
                    'location_type' => 'person',
                    'location_id' => $request->given_to . ($request->pin_number ? '-' . $request->pin_number : ''),
                    'parent_transaction_id' => $transaction->id
                ]);

                // Link book to transaction
                BookDistribution::create([
                    'stock_transaction_id' => $transaction->id,
                    'receipt_book_id' => $book->id
                ]);
            }

            DB::commit();

            Log::info('Books distributed to person', [
                'person' => $request->given_to,
                'pin' => $request->pin_number,
                'books' => "{$bookFrom}-{$bookTo}",
                'receipts' => $quantity
            ]);

            $bookDetails = $booksNeeded > 1
                ? "{$booksNeeded} books (Book #{$bookFrom}-#{$bookTo})"
                : "1 book (Book #{$bookFrom})";

            return back()->with('success', "Distributed {$bookDetails} = {$quantity} receipts to {$request->given_to}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Person distribution failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to distribute: ' . $e->getMessage());
        }
    }

    /**
     * Get Available Books at Branch (for Branch User)
     * Returns: Book details, total available
     */
    public function getAvailableBooks(Request $request)
    {
        try {
            $branchId = $request->branch_id ?? auth()->user()->branch_id;

            $books = ReceiptBook::where('location_type', 'branch')
                ->where('location_id', $branchId)
                ->where('status', 'distributed')
                ->with('lot')
                ->orderBy('lot_id')
                ->orderBy('book_number')
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'lot_number' => $book->lot->lot_number,
                        'lot_name' => $book->lot->lot_name,
                        'book_number' => $book->book_number,
                        'from_number' => $book->from_number,
                        'to_number' => $book->to_number,
                        'receipts' => $book->getTotalReceipts()
                    ];
                });

            $totalAvailable = $books->sum('receipts');
            $totalBooks = $books->count();

            // Group by lot
            $byLot = $books->groupBy('lot_number')->map(function ($lotBooks) {
                return [
                    'lot_number' => $lotBooks->first()['lot_number'],
                    'lot_name' => $lotBooks->first()['lot_name'],
                    'total_books' => $lotBooks->count(),
                    'total_receipts' => $lotBooks->sum('receipts'),
                    'books' => $lotBooks->values()
                ];
            })->values();

            return response()->json([
                'success' => true,
                'books' => $books,
                'by_lot' => $byLot,
                'summary' => [
                    'total_books' => $totalBooks,
                    'total_receipts' => $totalAvailable
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get available books', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Available Books in Lot (for Super Admin)
     * Returns: Available books in head office for selected lot
     */
    public function getLotBooks(Request $request, $lotId)
    {
        try {
            $lot = Lot::findOrFail($lotId);

            $books = ReceiptBook::where('lot_id', $lotId)
                ->where('status', 'available')
                ->where('location_type', 'head_office')
                ->orderBy('book_number')
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'book_number' => $book->book_number,
                        'from_number' => $book->from_number,
                        'to_number' => $book->to_number,
                        'receipts' => $book->getTotalReceipts()
                    ];
                });

            if ($books->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No books available in this lot at head office'
                ], 404);
            }

            $bookFrom = $books->first()['book_number'];
            $bookTo = $books->last()['book_number'];
            $totalBooks = $books->count();
            $totalReceipts = $books->sum('receipts');

            return response()->json([
                'success' => true,
                'lot' => [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'lot_name' => $lot->lot_name
                ],
                'books' => $books,
                'summary' => [
                    'total_books' => $totalBooks,
                    'total_receipts' => $totalReceipts,
                    'book_range' => "{$bookFrom} - {$bookTo}",
                    'receipt_range' => "{$books->first()['from_number']} - {$books->last()['to_number']}"
                ],
                'suggested_range' => [
                    'book_from' => $bookFrom,
                    'book_to' => $bookTo
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get lot books', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Branch Transactions (for modal/details view)
     */
    public function getBranchTransactions(Request $request, Branch $branch)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

            $transactions = StockTransaction::where('branch_id', $branch->id)
                ->with(['lot', 'bookDistributions.receiptBook'])
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                        'transaction_type' => $transaction->transaction_type,
                        'lot_number' => $transaction->lot->lot_number,
                        'book_from' => $transaction->book_from,
                        'book_to' => $transaction->book_to,
                        'receipt_from' => $transaction->receipt_from,
                        'receipt_to' => $transaction->receipt_to,
                        'total_books' => $transaction->total_books,
                        'total_receipts' => $transaction->total_receipts,
                        'given_to' => $transaction->given_to,
                        'pin_number' => $transaction->pin_number,
                        'received_by' => $transaction->received_by,
                        'remarks' => $transaction->remarks,
                    ];
                });

            return response()->json([
                'success' => true,
                'transactions' => $transactions
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get branch transactions', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Transaction (for corrections)
     */
    public function update(Request $request, $transactionId)
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date',
            'given_to' => 'nullable|string|max:255',
            'pin_number' => 'nullable|string|max:50',
            'received_by' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $transaction = StockTransaction::findOrFail($transactionId);

            // Only allow updating certain fields
            $transaction->update([
                'transaction_date' => $request->transaction_date,
                'given_to' => $request->given_to ?? $transaction->given_to,
                'pin_number' => $request->pin_number ?? $transaction->pin_number,
                'received_by' => $request->received_by ?? $transaction->received_by,
                'remarks' => $request->remarks ?? $transaction->remarks,
            ]);

            // If person info changed, update book locations
            if (
                $transaction->transaction_type === 'distribute_to_person' &&
                ($request->given_to || $request->pin_number)
            ) {

                $newLocationId = ($request->given_to ?? $transaction->given_to) .
                    ($request->pin_number ? '-' . $request->pin_number : '');

                ReceiptBook::whereIn(
                    'id',
                    $transaction->bookDistributions->pluck('receipt_book_id')
                )->update([
                    'location_id' => $newLocationId
                ]);
            }

            DB::commit();

            Log::info('Transaction updated', ['id' => $transaction->id]);

            return back()->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction update failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }

    /**
     * Delete Transaction (with book restoration)
     */
    public function destroy($transactionId)
    {
        try {
            DB::beginTransaction();

            $transaction = StockTransaction::findOrFail($transactionId);
            $transactionType = $transaction->transaction_type;

            // Get books from this transaction
            $bookDistributions = BookDistribution::where('stock_transaction_id', $transaction->id)
                ->with('receiptBook')
                ->get();

            // Restore books based on transaction type
            foreach ($bookDistributions as $distribution) {
                $book = $distribution->receiptBook;

                if ($transactionType === 'stock_in') {
                    // Delete books if stock was added
                    $book->delete();
                } elseif ($transactionType === 'distribute_to_branch') {
                    // Return to head office
                    $book->update([
                        'status' => 'available',
                        'location_type' => 'head_office',
                        'location_id' => null,
                        'parent_transaction_id' => null
                    ]);
                } elseif ($transactionType === 'distribute_to_person') {
                    // Return to branch
                    $book->update([
                        'status' => 'distributed',
                        'location_type' => 'branch',
                        'location_id' => $transaction->branch_id,
                        'parent_transaction_id' => null
                    ]);
                }
            }

            // Update inventory
            if ($transactionType === 'stock_in') {
                $inventory = HeadOfficeInventory::where('lot_id', $transaction->lot_id)->first();
                if ($inventory) {
                    $inventory->total_books -= $transaction->total_books;
                    $inventory->total_stock_in -= $transaction->total_receipts;
                    $inventory->total_stock = $inventory->total_stock_in - $inventory->total_stock_out;
                    $inventory->save();
                }
            } elseif ($transactionType === 'distribute_to_branch') {
                $inventory = HeadOfficeInventory::where('lot_id', $transaction->lot_id)->first();
                if ($inventory) {
                    $inventory->total_stock_out -= $transaction->total_receipts;
                    $inventory->total_stock = $inventory->total_stock_in - $inventory->total_stock_out;
                    $inventory->save();
                }
            }

            // Delete book distributions
            BookDistribution::where('stock_transaction_id', $transaction->id)->delete();

            // Delete transaction
            $transaction->delete();

            DB::commit();

            Log::info('Transaction deleted', [
                'id' => $transactionId,
                'type' => $transactionType
            ]);

            return back()->with('success', 'Transaction deleted and books restored successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transaction delete failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to delete: ' . $e->getMessage());
        }
    }

    /**
     * Get Stock Summary (for dashboard widgets)
     */
    public function getBranchSummary(Request $request)
    {
        try {
            $user = auth()->user();
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $branchId = $user->name === "Super Admin" ? $request->input('branch_id') : $user->branch_id;

            if (!$branchId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch ID required'
                ], 400);
            }

            $query = StockTransaction::where('branch_id', $branchId);

            if ($startDate && $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }

            $received = $query->clone()
                ->where('transaction_type', 'distribute_to_branch')
                ->sum('total_receipts');

            $distributed = $query->clone()
                ->where('transaction_type', 'distribute_to_person')
                ->sum('total_receipts');

            $currentAvailable = ReceiptBook::where('location_type', 'branch')
                ->where('location_id', $branchId)
                ->where('status', 'distributed')
                ->get()
                ->sum(fn($b) => $b->getTotalReceipts());

            return response()->json([
                'success' => true,
                'summary' => [
                    'total_received' => $received,
                    'total_distributed' => $distributed,
                    'current_available' => $currentAvailable,
                    'period_start' => $startDate,
                    'period_end' => $endDate
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get summary', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to PDF - Branch wise report
     */
    public function export(Request $request)
    {
        try {
            $user = auth()->user();
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $selectedBranch = $request->input('branch_id');

            // Base query for transactions
            $query = StockTransaction::with(['branch', 'lot'])
                ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('transaction_date', [$startDate, $endDate]);
                });

            // Apply branch filter based on user role
            if ($user->name === "Super Admin") {
                if ($selectedBranch) {
                    $query->where('branch_id', $selectedBranch);
                }
            } else {
                $query->where('branch_id', $user->branch_id);
            }

            $transactions = $query->orderBy('transaction_date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // Get branch name
            $branchName = $selectedBranch
                ? Branch::find($selectedBranch)->branch_name
                : ($user->name === "Super Admin" ? 'All Branches' : $user->branch->branch_name);

            // Calculate summaries
            $periodReceived = $transactions->where('transaction_type', 'distribute_to_branch')->sum('total_receipts');
            $periodDistributed = $transactions->where('transaction_type', 'distribute_to_person')->sum('total_receipts');

            // All time summary
            $allTimeQuery = StockTransaction::query();
            if ($user->name === "Super Admin") {
                if ($selectedBranch) {
                    $allTimeQuery->where('branch_id', $selectedBranch);
                }
            } else {
                $allTimeQuery->where('branch_id', $user->branch_id);
            }

            $allTimeReceived = $allTimeQuery->clone()->where('transaction_type', 'distribute_to_branch')->sum('total_receipts');
            $allTimeDistributed = $allTimeQuery->clone()->where('transaction_type', 'distribute_to_person')->sum('total_receipts');
            $currentAvailable = $allTimeReceived - $allTimeDistributed;

            $periodSummary = [
                'received' => $periodReceived,
                'distributed' => $periodDistributed
            ];

            $allTimeSummary = [
                'received' => $allTimeReceived,
                'distributed' => $allTimeDistributed,
                'available' => $currentAvailable
            ];

            // Generate PDF
            $pdf = PDF::loadView('pdf.payment-receipts', [
                'transactions' => $transactions,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'branchName' => $branchName,
                'periodSummary' => $periodSummary,
                'allTimeSummary' => $allTimeSummary
            ]);

            $sanitizedBranchName = str_replace(['/', '\\', ' '], '_', $branchName);
            $filename = "payment_receipts_{$sanitizedBranchName}_" .
                Carbon::parse($startDate)->format('Y_m_d') . "_to_" .
                Carbon::parse($endDate)->format('Y_m_d') . ".pdf";

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF export failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }

    /**
     * Generate Detailed Report (Landscape format)
     */
    public function generateReport(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'branch_id' => 'nullable|exists:branches,id'
            ]);

            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $branchId = $request->branch_id;

            // Get branch summaries
            $branches = Branch::select('branches.*')
                ->when($branchId, fn($q) => $q->where('branches.id', $branchId))
                ->get()
                ->map(function ($branch) use ($startDate, $endDate) {
                    $periodReceived = StockTransaction::where('branch_id', $branch->id)
                        ->where('transaction_type', 'distribute_to_branch')
                        ->whereBetween('transaction_date', [$startDate, $endDate])
                        ->sum('total_receipts');

                    $periodDistributed = StockTransaction::where('branch_id', $branch->id)
                        ->where('transaction_type', 'distribute_to_person')
                        ->whereBetween('transaction_date', [$startDate, $endDate])
                        ->sum('total_receipts');

                    $allTimeReceived = StockTransaction::where('branch_id', $branch->id)
                        ->where('transaction_type', 'distribute_to_branch')
                        ->sum('total_receipts');

                    $allTimeDistributed = StockTransaction::where('branch_id', $branch->id)
                        ->where('transaction_type', 'distribute_to_person')
                        ->sum('total_receipts');

                    $currentAvailable = ReceiptBook::where('location_type', 'branch')
                        ->where('location_id', $branch->id)
                        ->where('status', 'distributed')
                        ->get()
                        ->sum(fn($b) => $b->getTotalReceipts());

                    return [
                        'id' => $branch->id,
                        'branch_name' => $branch->branch_name,
                        'branch_code' => $branch->branch_code,
                        'period_received' => $periodReceived,
                        'period_distributed' => $periodDistributed,
                        'all_time_received' => $allTimeReceived,
                        'all_time_distributed' => $allTimeDistributed,
                        'current_available' => $currentAvailable
                    ];
                });

            // Get transactions ordered by date ascending first to calculate running balance
            $transactions = StockTransaction::with(['branch', 'lot'])
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->orderBy('transaction_date', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Calculate running balance for each branch
            $branchBalances = [];
            $formattedTransactions = $transactions->map(function ($transaction) use (&$branchBalances) {
                $branchId = $transaction->branch_id;
                if (!isset($branchBalances[$branchId])) {
                    // Get initial balance at start date by calculating all previous transactions
                    $previousBalance = StockTransaction::where('branch_id', $branchId)
                        ->where('transaction_date', '<', $transaction->transaction_date)
                        ->get()
                        ->reduce(function ($balance, $tx) {
                            if ($tx->transaction_type === 'distribute_to_branch') {
                                return $balance + $tx->total_receipts;
                            } elseif ($tx->transaction_type === 'distribute_to_person') {
                                return $balance - $tx->total_receipts;
                            }
                            return $balance;
                        }, 0);
                    $branchBalances[$branchId] = $previousBalance;
                }

                // Update running balance
                if ($transaction->transaction_type === 'distribute_to_branch') {
                    $branchBalances[$branchId] += $transaction->total_receipts;
                } elseif ($transaction->transaction_type === 'distribute_to_person') {
                    $branchBalances[$branchId] -= $transaction->total_receipts;
                }

                return [
                    'transaction_date' => $transaction->transaction_date,
                    'branch_name' => optional($transaction->branch)->branch_name ?? 'N/A',
                    'receive_quantity' => $transaction->transaction_type === 'distribute_to_branch' ? $transaction->total_receipts : null,
                    'given_quantity' => $transaction->transaction_type === 'distribute_to_person' ? $transaction->total_receipts : null,
                    'available_receipts' => $branchBalances[$branchId],
                    'receipt_book_number' => $transaction->book_from . '-' . $transaction->book_to
                ];
            });

            // Re-sort transactions to display in descending order
            $transactions = $formattedTransactions->sortByDesc('transaction_date')->values();

            // Calculate totals
            $totals = [
                'total_period_received' => $branches->sum('period_received'),
                'total_period_distributed' => $branches->sum('period_distributed'),
                'total_available' => $branches->sum('current_available'),
                'total_branches' => $branches->count(),
            ];

            $data = [
                'meta' => [
                    'start_date' => Carbon::parse($startDate)->format('d/m/Y'),
                    'end_date' => Carbon::parse($endDate)->format('d/m/Y'),
                    'generated_at' => now()->format('d/m/Y H:i:s'),
                    'branch' => $branchId ? $branches->first()['branch_name'] : 'All Branches'
                ],
                'branches' => $branches,
                'transactions' => $transactions,
                'totals' => $totals
            ];

            $pdf = Pdf::loadView('reports.payment-receipts', $data);

            $filename = 'payment_receipts_' .
                ($branchId ? strtolower(str_replace(' ', '_', $branches->first()['branch_name'])) : 'all_branches') . '_' .
                Carbon::parse($startDate)->format('Y_m_d') . '_to_' .
                Carbon::parse($endDate)->format('Y_m_d') . '.pdf';

            return $pdf->setPaper('a4', 'landscape')->download($filename);
        } catch (\Exception $e) {
            Log::error('Report generation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Get All Active Lots (for dropdowns)
     */
    public function getActiveLots()
    {
        try {
            $lots = Lot::where('is_active', true)
                ->withCount([
                    'receiptBooks as available_books' => function ($q) {
                        $q->where('status', 'available')
                            ->where('location_type', 'head_office');
                    }
                ])
                ->get()
                ->map(function ($lot) {
                    $availableReceipts = ReceiptBook::where('lot_id', $lot->id)
                        ->where('status', 'available')
                        ->where('location_type', 'head_office')
                        ->sum(DB::raw('(to_number - from_number + 1)'));

                    return [
                        'id' => $lot->id,
                        'lot_number' => $lot->lot_number,
                        'lot_name' => $lot->lot_name,
                        'available_books' => $lot->available_books,
                        'available_receipts' => $availableReceipts,
                        'has_stock' => $lot->available_books > 0
                    ];
                });

            return response()->json([
                'success' => true,
                'lots' => $lots
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get lots', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create New Lot (for Super Admin)
     */
    public function createLot(Request $request)
    {
        $validated = $request->validate([
            'lot_name' => 'required|string|max:255'
        ]);

        try {
            $lot = Lot::create([
                'lot_number' => Lot::generateNextLotNumber(),
                'lot_name' => $request->lot_name,
                'is_active' => true
            ]);

            Log::info('New lot created', ['lot' => $lot->lot_number]);

            return response()->json([
                'success' => true,
                'lot' => [
                    'id' => $lot->id,
                    'lot_number' => $lot->lot_number,
                    'lot_name' => $lot->lot_name
                ],
                'message' => "Lot {$lot->lot_number} created successfully"
            ]);
        } catch (\Exception $e) {
            Log::error('Lot creation failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create lot: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle Lot Status (activate/deactivate)
     */
    public function toggleLotStatus($lotId)
    {
        try {
            $lot = Lot::findOrFail($lotId);
            $lot->is_active = !$lot->is_active;
            $lot->save();

            Log::info('Lot status toggled', [
                'lot' => $lot->lot_number,
                'status' => $lot->is_active ? 'active' : 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'lot' => $lot,
                'message' => "Lot {$lot->lot_number} " . ($lot->is_active ? 'activated' : 'deactivated')
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to toggle lot status', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get Transaction Details (for modal view)
     */
    public function getTransactionDetails($transactionId)
    {
        try {
            $transaction = StockTransaction::with([
                'lot',
                'branch',
                'bookDistributions.receiptBook'
            ])->findOrFail($transactionId);

            $books = $transaction->bookDistributions->map(function ($dist) {
                $book = $dist->receiptBook;
                return [
                    'book_number' => $book->book_number,
                    'from_number' => $book->from_number,
                    'to_number' => $book->to_number,
                    'receipts' => $book->getTotalReceipts(),
                    'status' => $book->status
                ];
            });

            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $transaction->id,
                    'transaction_type' => $transaction->transaction_type,
                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                    'lot_number' => $transaction->lot->lot_number,
                    'lot_name' => $transaction->lot->lot_name,
                    'branch_name' => $transaction->branch->branch_name ?? 'N/A',
                    'book_from' => $transaction->book_from,
                    'book_to' => $transaction->book_to,
                    'receipt_from' => $transaction->receipt_from,
                    'receipt_to' => $transaction->receipt_to,
                    'total_books' => $transaction->total_books,
                    'total_receipts' => $transaction->total_receipts,
                    'given_to' => $transaction->given_to,
                    'pin_number' => $transaction->pin_number,
                    'received_by' => $transaction->received_by,
                    'remarks' => $transaction->remarks,
                    'books' => $books
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get transaction details', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
