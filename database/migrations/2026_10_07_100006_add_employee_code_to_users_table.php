<?php

use App\Support\EmployeeCode;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code', 20)->nullable()->unique()->after('id');
        });

        // Put everyone already on file into the shared EMP- series, oldest first.
        $people = DB::table('workers')->select('id', 'created_at', DB::raw("'workers' as tbl"))
            ->unionAll(DB::table('users')->select('id', 'created_at', DB::raw("'users' as tbl")))
            ->get()
            ->sortBy([['created_at', 'asc'], ['tbl', 'desc'], ['id', 'asc']])
            ->values();

        foreach ($people as $index => $person) {
            DB::table($person->tbl)->where('id', $person->id)->update([
                $person->tbl === 'workers' ? 'code' : 'employee_code' => EmployeeCode::format($index + 1),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['employee_code']);
            $table->dropColumn('employee_code');
        });
    }
};
