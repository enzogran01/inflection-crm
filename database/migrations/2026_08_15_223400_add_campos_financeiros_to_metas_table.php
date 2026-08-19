<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->string('categoria')->nullable()->after('status');
            $table->string('tipo_meta_financeira')->nullable()->after('categoria');
            $table->decimal('valor_alvo', 15, 2)->nullable()->after('tipo_meta_financeira');
            $table->string('periodicidade')->nullable()->after('valor_alvo');
            $table->date('data_referencia')->nullable()->after('periodicidade');
        });
    }

    public function down(): void
    {
        Schema::table('metas', function (Blueprint $table) {
            $table->dropColumn([
                'categoria',
                'tipo_meta_financeira',
                'valor_alvo',
                'periodicidade',
                'data_referencia',
            ]);
        });
    }
};
