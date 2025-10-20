<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookDistribution extends Model
{
    protected $fillable = [
        'stock_transaction_id',
        'receipt_book_id'
    ];

    public function stockTransaction(): BelongsTo
    {
        return $this->belongsTo(StockTransaction::class);
    }

    public function receiptBook(): BelongsTo
    {
        return $this->belongsTo(ReceiptBook::class);
    }
}
