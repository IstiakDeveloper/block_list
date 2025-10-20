<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptBook extends Model
{
    protected $fillable = [
        'lot_id',
        'book_number',
        'from_number',
        'to_number',
        'status',
        'location_type',
        'location_id',
        'parent_transaction_id'
    ];

    protected $casts = [
        'book_number' => 'integer',
        'from_number' => 'integer',
        'to_number' => 'integer'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class, 'parent_transaction_id');
    }

    public function distributions()
    {
        return $this->hasMany(BookDistribution::class);
    }

    // Calculate total receipts in this book
    public function getTotalReceipts(): int
    {
        return $this->to_number - $this->from_number + 1;
    }

    // Scope: Available books at head office
    public function scopeAvailableAtHeadOffice($query)
    {
        return $query->where('status', 'available')
            ->where('location_type', 'head_office');
    }

    // Scope: Available books at specific branch
    public function scopeAvailableAtBranch($query, $branchId)
    {
        return $query->where('status', 'available')
            ->where('location_type', 'branch')
            ->where('location_id', $branchId);
    }

    // Scope: Books in specific lot
    public function scopeInLot($query, $lotId)
    {
        return $query->where('lot_id', $lotId);
    }
}
