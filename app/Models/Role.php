<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active'
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->permissions === null) {
            return false;
        }

        return in_array('*', $this->permissions) || in_array($permission, $this->permissions);
    }

    /**
     * Check if the role has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->permissions === null) {
            return false;
        }

        if (in_array('*', $this->permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (in_array($permission, $this->permissions)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the role has all of the given permissions.
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->permissions === null) {
            return false;
        }

        if (in_array('*', $this->permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if (!in_array($permission, $this->permissions)) {
                return false;
            }
        }

        return true;
    }
}
