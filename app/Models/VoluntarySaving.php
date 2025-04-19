<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoluntarySaving extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_date',
        'branch_id',
        'somiti_name',
        'somiti_code',
        'member_name',
        'member_code',
        'profit',
        'member_mobile',
        'applicant_name',
        'designation',
        'pin',
        'signature',
        'status',
    ];

    public function deposits()
    {
        return $this->hasMany(VoluntarySavingDeposit::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
