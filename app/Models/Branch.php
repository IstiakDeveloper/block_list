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

    /**
     * Match branch_code across formats (e.g. MisLoan "0003" vs block_list "003").
     */
    public static function findByFlexibleCode(string $branchCode): ?self
    {
        $code = trim($branchCode);
        if ($code === '') {
            return null;
        }

        $normalized = ltrim($code, '0') ?: '0';
        $candidates = array_values(array_unique([
            $code,
            $normalized,
            str_pad($normalized, 3, '0', STR_PAD_LEFT),
            str_pad($normalized, 4, '0', STR_PAD_LEFT),
        ]));

        return static::query()
            ->whereIn('branch_code', $candidates)
            ->first();
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

    public function branchOfficers()
    {
        return $this->hasMany(BranchOfficer::class);
    }

    public function receiptStock()
    {
        return $this->hasOne(ReceiptStock::class);
    }

    public function receiptDistributions()
    {
        return $this->hasMany(OfficerReceiptDistribution::class);
    }

    public function voluntarySavings()
    {
        return $this->hasMany(VoluntarySaving::class);
    }

}
