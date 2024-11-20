<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = ['branch_name', 'branch_code', 'address'];



    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_user', 'branch_id', 'user_id');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function branchUsers()
    {
        return $this->belongsToMany(User::class, 'branch_user');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('branch_name', 'like', "%{$search}%")
                ->orWhere('branch_code', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        });
    }

    public function scopeSort($query, $field, $direction = 'asc')
    {
        $validFields = ['branch_name', 'branch_code', 'address', 'created_at'];

        if (in_array($field, $validFields)) {
            return $query->orderBy($field, $direction);
        }

        return $query;
    }
}
