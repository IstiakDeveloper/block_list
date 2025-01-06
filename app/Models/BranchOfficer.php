<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchOfficer extends Model
{
    protected $fillable = ['branch_id', 'name', 'phone_number', 'pin_number', 'details', 'is_active'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function distributions()
    {
        return $this->hasMany(OfficerReceiptDistribution::class);
    }

}
