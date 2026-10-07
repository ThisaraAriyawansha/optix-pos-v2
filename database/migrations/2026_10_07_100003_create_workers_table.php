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
        Schema::create('workers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('nic', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('address')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            // 'daily', 'piece_rate' or 'monthly'
            $table->string('pay_type', 20)->default('piece_rate');
            $table->decimal('daily_rate', 10, 2)->nullable();
            $table->decimal('monthly_salary', 12, 2)->nullable();
            $table->date('joined_on')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
