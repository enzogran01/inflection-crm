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
        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->enum('type', ['receita', 'despesa']);
            $table->string('category')->nullable();
            $table->integer('amount');
            $table->enum('periodicity', ['mensal', 'anual'])->default('mensal');
            $table->enum('payment_method', ['credito', 'debito', 'pix', 'dinheiro', 'boleto'])->nullable();
            $table->integer('due_day')->nullable()->comment('Day of the month for monthly periodicity');
            $table->integer('due_month')->nullable()->comment('Month of the year for yearly periodicity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
    }
};
