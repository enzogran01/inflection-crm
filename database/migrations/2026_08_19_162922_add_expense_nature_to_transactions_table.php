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
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('expense_nature', ['fixa', 'variavel'])->nullable()->after('category');
        });

        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->enum('expense_nature', ['fixa', 'variavel'])->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recurring_transactions', function (Blueprint $table) {
            $table->dropColumn('expense_nature');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('expense_nature');
        });
    }
};
