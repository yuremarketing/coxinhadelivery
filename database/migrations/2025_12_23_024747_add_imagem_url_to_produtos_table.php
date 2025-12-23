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
        Schema::table('produtos', function (Blueprint $table) {
            // Adiciona a coluna imagem_url
            // nullable() permite que o produto fique sem imagem se necessário
            // after('preco') tenta colocar a coluna logo após o preço (opcional, apenas para organização)
            $table->string('imagem_url')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produtos', function (Blueprint $table) {
            // Remove a coluna caso você precise desfazer a migration
            $table->dropColumn('imagem_url');
        });
    }
};