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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit', 30);
            $table->text('description')->nullable();

            // Cost snapshot, recalculated from materials and activity rates on every save.
            $table->decimal('material_cost', 12, 2)->default(0);
            $table->decimal('labour_cost', 12, 2)->default(0);
            $table->decimal('handling_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            $table->string('commission_type', 10)->default('fixed');
            $table->decimal('commission_value', 12, 2)->default(0);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->restrictOnDelete();
            $table->decimal('quantity', 12, 3);
            $table->timestamps();

            $table->unique(['product_id', 'raw_material_id']);
        });

        Schema::create('product_activity_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('work_activity_id')->constrained('work_activities')->restrictOnDelete();
            $table->decimal('rate', 10, 2);
            $table->timestamps();

            $table->unique(['product_id', 'work_activity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_activity_rates');
        Schema::dropIfExists('product_materials');
        Schema::dropIfExists('products');
    }
};
