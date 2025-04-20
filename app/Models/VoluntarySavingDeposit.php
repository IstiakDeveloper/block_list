<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoluntarySavingDeposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'voluntary_saving_id',
        'deposit_date',
        'account_name',
        'deposit_amount',
        'profit',
    ];

    public function voluntarySaving()
    {
        return $this->belongsTo(VoluntarySaving::class);
    }
}
