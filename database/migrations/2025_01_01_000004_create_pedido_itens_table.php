<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedido_itens', function (Blueprint $table) {
            $table->id();
            
            // Ligações
            $table->foreignId('pedido_id')->constrained('pedidos')->onDelete('cascade');
            $table->foreignId('produto_id')->constrained('produtos');
            
            // Dados essenciais
            $table->integer('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            
            // REMOVEMOS O 'subtotal' DAQUI POIS ELE ERA O CAUSADOR DO ERRO
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedido_itens');
    }
};
