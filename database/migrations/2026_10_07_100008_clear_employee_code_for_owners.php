<?php

use App\Models\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Owners (Super Admin / Admin) don't work shifts, so they carry no employee ID.
     */
    public function up(): void
    {
        $ownerRoleIds = DB::table('user_roles')->whereIn('name', UserRole::ADMIN_ROLES)->pluck('id');

        DB::table('users')->whereIn('role_id', $ownerRoleIds)->update(['employee_code' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
