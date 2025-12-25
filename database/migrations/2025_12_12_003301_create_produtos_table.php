<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela de Produtos.
     * Aqui fica o coração do cardápio. Decidi juntar todas as informações
     * essenciais em um lugar só para manter a organização.
     */
    public function up(): void
    {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            
            // Dados Básicos
            $table->string('nome');
            $table->text('descricao');
            $table->decimal('preco', 10, 2);
            $table->string('categoria'); // Ex: Salgados, Bebidas
            
            // Controle de Estoque (Decidi iniciar zerado por segurança)
            $table->integer('quantidade')->default(0);
            
            // Marketing (Foto para o App)
            $table->string('imagem_url')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produtos');
    }
};
