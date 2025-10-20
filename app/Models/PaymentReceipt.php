<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'stock_transaction_id',
        'lot_id',
        'branch_id',
        'transaction_date',
        'receive_quantity',
        'receipt_from_number',
        'receipt_to_number',
        'total_cumulative_quantity',
        'received_by',
        'given_to',
        'pin_number',
        'given_from_number',
        'given_to_number',
        'receipt_book_number',
        'given_quantity',
        'available_receipts'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'receive_quantity' => 'integer',
        'receipt_from_number' => 'integer',
        'receipt_to_number' => 'integer',
        'total_cumulative_quantity' => 'integer',
        'given_from_number' => 'integer',
        'given_to_number' => 'integer',
        'given_quantity' => 'integer',
        'available_receipts' => 'integer'
    ];

    public function stockTransaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Helper method to calculate available receipts
    public function calculateAvailableReceipts(): int
    {
        return $this->receive_quantity - $this->given_quantity;
    }

    // Scope to get latest record for a branch
    public function scopeLatestForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');
    }
}
