<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lot extends Model
{
    protected $fillable = [
        'lot_number',
        'lot_name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function receiptBooks(): HasMany
    {
        return $this->hasMany(ReceiptBook::class);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function headOfficeInventories(): HasMany
    {
        return $this->hasMany(HeadOfficeInventory::class);
    }

    // Get available books at head office
    public function getAvailableBooksAtHeadOffice()
    {
        return $this->receiptBooks()
            ->where('status', 'available')
            ->where('location_type', 'head_office')
            ->orderBy('book_number')
            ->get();
    }

    // Get total available receipts
    public function getTotalAvailableReceipts()
    {
        return $this->receiptBooks()
            ->where('status', 'available')
            ->where('location_type', 'head_office')
            ->sum(\DB::raw('(to_number - from_number + 1)'));
    }

    // Generate next lot number
    public static function generateNextLotNumber(): string
    {
        $lastLot = self::orderBy('id', 'desc')->first();
        if (!$lastLot) {
            return 'Lot-1';
        }

        $number = (int) str_replace('Lot-', '', $lastLot->lot_number);
        return 'Lot-' . ($number + 1);
    }
}
