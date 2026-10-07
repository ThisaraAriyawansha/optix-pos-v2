<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    /** Roles with full access (costs, pricing, attendance admin). Not employees unless set so. */
    public const ADMIN_ROLES = ['Super Admin', 'Admin'];

    protected $fillable = ['name', 'is_employee'];

    protected function casts(): array
    {
        return [
            'is_employee' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        // When nobody chose, admin roles are owners and every other role is staff.
        static::creating(function (UserRole $role) {
            $role->is_employee ??= ! in_array($role->name, self::ADMIN_ROLES, true);
        });

        // Give or take away Employee IDs when the answer changes.
        static::updated(function (UserRole $role) {
            if ($role->wasChanged('is_employee')) {
                $role->users()->get()->each(function (User $user) use ($role) {
                    $user->track_attendance = $user->track_attendance && $role->is_employee;
                    $user->save();
                });
            }
        });
    }

    /**
     * Employees get an Employee ID and can check in / out. Users without a role count as employees.
     */
    public static function isEmployeeRole($roleId): bool
    {
        return ! $roleId || (bool) static::whereKey($roleId)->value('is_employee');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
