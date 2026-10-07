<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\EmployeeCode;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['employee_code', 'name', 'email', 'password', 'phone_number', 'address', 'role_id', 'status', 'branch_id', 'track_attendance'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
            'status' => 'boolean',
            'track_attendance' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (User $user) {
            $user->employee_code = EmployeeCode::normalize($user->employee_code) ?? EmployeeCode::next();
        });
    }

    public function role()
    {
        return $this->belongsTo(UserRole::class, 'role_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Admins and super admins see product costs and set selling prices and pay rates.
     */
    public function isAdmin(): bool
    {
        return in_array($this->role?->name, UserRole::ADMIN_ROLES, true);
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendable');
    }

    /** Active non-admin staff. Admins and super admins never check in or out. */
    public function scopeAttendanceStaff(Builder $query): void
    {
        $query->where('status', true)
            ->where(fn ($query) => $query->whereNull('role_id')
                ->orWhereHas('role', fn ($role) => $role->whereNotIn('name', UserRole::ADMIN_ROLES)));
    }

    /** Staff who are ticked to check in and out. */
    public function scopeAttendanceTracked(Builder $query): void
    {
        $query->attendanceStaff()->where('track_attendance', true);
    }
}
