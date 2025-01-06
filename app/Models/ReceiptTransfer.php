<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceiptTransfer extends Model
{
    protected $fillable = [
        'from_branch_id', 'to_branch_id', 'user_id',
        'quantity', 'notes', 'transfer_date'
    ];

    protected $dates = ['transfer_date'];

    public function fromBranch()
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    public function toBranch()
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
