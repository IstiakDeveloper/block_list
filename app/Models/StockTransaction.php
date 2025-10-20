<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StockTransaction extends Model
{
    protected $fillable = [
        'lot_id',
        'transaction_type',
        'branch_id',
        'transaction_date',
        'book_from',
        'book_to',
        'receipt_from',
        'receipt_to',
        'total_books',
        'total_receipts',
        'given_to',
        'pin_number',
        'received_by',
        'remarks'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'book_from' => 'integer',
        'book_to' => 'integer',
        'receipt_from' => 'integer',
        'receipt_to' => 'integer',
        'total_books' => 'integer',
        'total_receipts' => 'integer'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function bookDistributions(): HasMany
    {
        return $this->hasMany(BookDistribution::class);
    }

    public function receiptBooks(): BelongsToMany
    {
        return $this->belongsToMany(ReceiptBook::class, 'book_distributions');
    }

    // Calculate total books
    public static function calculateTotalBooks($bookFrom, $bookTo): int
    {
        return $bookTo - $bookFrom + 1;
    }

    // Calculate receipt numbers from book numbers
    public static function calculateReceiptNumbers($bookFrom, $bookTo): array
    {
        $receiptFrom = (($bookFrom - 1) * 100) + 1;
        $receiptTo = $bookTo * 100;
        $totalReceipts = ($bookTo - $bookFrom + 1) * 100;

        return [
            'receipt_from' => $receiptFrom,
            'receipt_to' => $receiptTo,
            'total_receipts' => $totalReceipts
        ];
    }

    // Scope: Transactions by type
    public function scopeByType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    // Scope: Transactions for branch
    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
