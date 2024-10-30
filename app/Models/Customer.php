<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'branch_id', 'nid_part_1', 'nid_part_2', 'name', 'name_bn',
        'father_name', 'mother_name', 'dob', 'nid_number', 'address', 'details','phone_number', 'rejected_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class); // Adjust as needed
    }


}
