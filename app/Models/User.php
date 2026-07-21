<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'photo',
        'email',
        'role',
        'branch_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }



    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_user', 'user_id', 'branch_id');
    }

    /**
     * Branches this user may access (pivot assignments + main branch).
     *
     * @return \Illuminate\Support\Collection<int, Branch>
     */
    public function authorizedBranches(): \Illuminate\Support\Collection
    {
        if ($this->name === 'Super Admin') {
            return Branch::query()->orderBy('branch_name')->get();
        }

        $branchIds = $this->branches()->pluck('branches.id');

        if ($this->branch_id) {
            $branchIds->push($this->branch_id);
        }

        $uniqueIds = $branchIds->unique()->filter()->values();

        if ($uniqueIds->isEmpty()) {
            return collect();
        }

        return Branch::query()
            ->whereIn('id', $uniqueIds)
            ->orderBy('branch_name')
            ->get();
    }

    public function canAccessBranch(int $branchId): bool
    {
        if ($this->name === 'Super Admin') {
            return true;
        }

        if ($this->branches()->where('branches.id', $branchId)->exists()) {
            return true;
        }

        return (int) $this->branch_id === $branchId;
    }
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('branch', function($bq) use ($search) {
                    $bq->where('branch_name', 'like', "%{$search}%");
                });
        });
    }

    public function scopeSort($query, $field, $direction = 'asc')
    {
        $validFields = ['name', 'email', 'created_at'];

        if (in_array($field, $validFields)) {
            return $query->orderBy($field, $direction);
        }

        // Special case for sorting by branch name
        if ($field === 'branch_name') {
            return $query->join('branches', 'users.branch_id', '=', 'branches.id')
                ->orderBy('branches.branch_name', $direction)
                ->select('users.*');
        }

        return $query;
    }

    /**
     * Distinct non-empty role values currently stored in the users table.
     *
     * @return array<int, string>
     */
    public static function distinctRolesFromDatabase(): array
    {
        return static::query()
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->orderBy('role')
            ->pluck('role')
            ->values()
            ->all();
    }
}
