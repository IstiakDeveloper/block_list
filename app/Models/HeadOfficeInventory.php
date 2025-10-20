<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HeadOfficeInventory extends Model
{
    protected $fillable = [
        'lot_id',
        'total_books',
        'total_stock',
        'total_stock_in',
        'total_stock_out'
    ];

    protected $casts = [
        'total_books' => 'integer',
        'total_stock' => 'integer',
        'total_stock_in' => 'integer',
        'total_stock_out' => 'integer'
    ];

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    // Get current available stock
    public function getAvailableStock(): int
    {
        return $this->total_stock_in - $this->total_stock_out;
    }

    // Update stock when receiving
    public function addStock($books, $receipts)
    {
        $this->total_books += $books;
        $this->total_stock_in += $receipts;
        $this->total_stock = $this->total_stock_in - $this->total_stock_out;
        $this->save();
    }

    // Update stock when distributing
    public function removeStock($books, $receipts)
    {
        $this->total_stock_out += $receipts;
        $this->total_stock = $this->total_stock_in - $this->total_stock_out;
        $this->save();
    }
}
