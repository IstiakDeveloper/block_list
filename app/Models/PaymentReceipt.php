<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
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

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Helper method to calculate available receipts
    public function calculateAvailableReceipts(): int
    {
        return $this->receive_quantity - $this->given_quantity;
    }

    // Helper method to validate receipt numbers
    public function validateReceiptNumbers(): bool
    {
        if ($this->receive_quantity > 0) {
            return ($this->receipt_to_number - $this->receipt_from_number + 1) === $this->receive_quantity;
        }
        if ($this->given_quantity > 0) {
            return ($this->given_to_number - $this->given_from_number + 1) === $this->given_quantity;
        }
        return true;
    }

    // Scope to get latest record for a branch
    public function scopeLatestForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc');
    }
}
