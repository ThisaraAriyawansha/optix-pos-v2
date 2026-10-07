<?php

use App\Models\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Each role says whether its people are employees (Employee ID, check-in / check-out)
     * or owners who are not.
     */
    public function up(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->boolean('is_employee')->default(true)->after('name');
        });

        $ownerRoleIds = DB::table('user_roles')->whereIn('name', UserRole::ADMIN_ROLES)->pluck('id');

        DB::table('user_roles')->whereIn('id', $ownerRoleIds)->update(['is_employee' => false]);
        DB::table('users')->whereIn('role_id', $ownerRoleIds)->update(['employee_code' => null, 'track_attendance' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table) {
            $table->dropColumn('is_employee');
        });
    }
};
