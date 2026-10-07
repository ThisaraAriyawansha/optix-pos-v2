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
        Schema::create('work_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            // 'labour' (making the product) or 'handling' (sorting, transport, loading...)
            $table->string('cost_group', 20)->default('labour');
            // Quantities of this activity count as units produced / units dispatched in reports.
            $table->boolean('is_production')->default(false);
            $table->boolean('is_dispatch')->default(false);
            $table->decimal('default_rate', 10, 2)->default(0);
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_activities');
    }
};
