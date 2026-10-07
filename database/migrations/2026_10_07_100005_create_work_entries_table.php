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
        Schema::create('work_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worker_id')->constrained('workers')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('salary_payment_id')->nullable()->constrained('salary_payments')->nullOnDelete();
            $table->date('work_date');
            // 'present', 'half_day' or 'absent'
            $table->string('attendance', 20)->default('present');
            $table->decimal('daily_wage', 12, 2)->default(0);
            $table->decimal('piece_earnings', 12, 2)->default(0);
            $table->decimal('total_earnings', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['worker_id', 'work_date']);
            $table->index(['work_date', 'branch_id']);
        });

        Schema::create('work_entry_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_entry_id')->constrained('work_entries')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('work_activity_id')->constrained('work_activities')->restrictOnDelete();
            $table->decimal('quantity', 12, 2);
            $table->decimal('rate', 10, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_entry_items');
        Schema::dropIfExists('work_entries');
    }
};
