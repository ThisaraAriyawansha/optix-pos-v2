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
        // Only people whose role is marked "employee" carry an Employee ID; they always have
        // one, including someone moved from an owner role.
        static::saving(function (User $user) {
            $user->employee_code = UserRole::isEmployeeRole($user->role_id)
                ? EmployeeCode::normalize($user->employee_code) ?? EmployeeCode::next()
                : null;
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

    /** False when the user's role is marked as not an employee (e.g. owners). */
    public function isEmployee(): bool
    {
        return $this->role?->is_employee ?? true;
    }

    /** Active staff in employee roles. Owners never check in or out. */
    public function scopeAttendanceStaff(Builder $query): void
    {
        $query->where('status', true)
            ->where(fn ($query) => $query->whereNull('role_id')
                ->orWhereHas('role', fn ($role) => $role->where('is_employee', true)));
    }

    /** Staff who are ticked to check in and out. */
    public function scopeAttendanceTracked(Builder $query): void
    {
        $query->attendanceStaff()->where('track_attendance', true);
    }
}
