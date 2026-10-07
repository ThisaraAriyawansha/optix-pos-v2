<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Who is checked in / out. Admins and super admins are never tracked, whatever this says.
        Schema::table('workers', function (Blueprint $table) {
            $table->boolean('track_attendance')->default(true)->after('is_active');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('track_attendance')->default(true)->after('status');
        });

        // One row per shift: opened on check-in, closed on check-out.
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            // 'worker' (labourer) or 'user' (managing staff)
            $table->morphs('attendable');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('work_date');
            $table->dateTime('check_in_at');
            $table->dateTime('check_out_at')->nullable();
            // 'manual' or 'fingerprint'
            $table->string('check_in_method', 20)->default('manual');
            $table->string('check_out_method', 20)->nullable();
            $table->foreignId('check_in_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('check_out_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('device_sn', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['work_date', 'branch_id']);
            $table->index(['attendable_type', 'attendable_id', 'work_date']);
        });

        // Raw fingerprint-device log. Devices re-send old logs after a reconnect, so the
        // unique key makes every punch count once.
        Schema::create('attendance_punches', function (Blueprint $table) {
            $table->id();
            $table->string('device_sn', 50);
            $table->string('pin', 30);
            $table->dateTime('punched_at');
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            // 'check_in', 'check_out', 'duplicate' or 'unknown' (no tracked person with that ID)
            $table->string('result', 20);
            $table->timestamps();

            $table->unique(['device_sn', 'pin', 'punched_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_punches');
        Schema::dropIfExists('attendances');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('track_attendance');
        });

        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn('track_attendance');
        });
    }
};
