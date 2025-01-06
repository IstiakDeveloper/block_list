<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfficerReceiptDistribution extends Model
{
    protected $fillable = [
        'branch_id', 'branch_officer_id', 'user_id',
        'quantity', 'notes', 'distribution_date'
    ];

    protected $dates = ['distribution_date'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function officer()
    {
        return $this->belongsTo(BranchOfficer::class, 'branch_officer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
