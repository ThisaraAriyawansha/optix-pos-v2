<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * EPF / ETF is set per worker (some are not covered), and each salary payment
     * keeps the amounts worked out at the time so later rate changes don't rewrite history.
     */
    public function up(): void
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->boolean('epf_enabled')->default(false)->after('monthly_salary');
            $table->string('epf_number', 30)->nullable()->after('epf_enabled');
            $table->decimal('epf_employee_rate', 5, 2)->default(8)->after('epf_number');
            $table->decimal('epf_employer_rate', 5, 2)->default(12)->after('epf_employee_rate');
            $table->decimal('etf_rate', 5, 2)->default(3)->after('epf_employer_rate');
        });

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->decimal('epf_base', 12, 2)->default(0)->after('deductions');
            $table->decimal('epf_employee', 12, 2)->default(0)->after('epf_base');
            $table->decimal('epf_employer', 12, 2)->default(0)->after('epf_employee');
            $table->decimal('etf', 12, 2)->default(0)->after('epf_employer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_payments', function (Blueprint $table) {
            $table->dropColumn(['epf_base', 'epf_employee', 'epf_employer', 'etf']);
        });

        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn(['epf_enabled', 'epf_number', 'epf_employee_rate', 'epf_employer_rate', 'etf_rate']);
        });
    }
};
