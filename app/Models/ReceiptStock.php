<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptStock extends Model
{
    protected $fillable = ['branch_id', 'total_receipts', 'used_receipts', 'available_receipts'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
