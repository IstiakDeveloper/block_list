<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficerReceiptDistribution extends Model
{
    protected $fillable = [
        'branch_id', 'branch_officer_id', 'user_id',
        'quantity', 'notes', 'distribution_date'
    ];

    protected $dates = ['distribution_date'];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Fixed relationship name from branchOfficer to branchOfficer
    public function officer(): BelongsTo
    {
        return $this->belongsTo(BranchOfficer::class, 'branch_officer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
