<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela 'pedido_itens' (tabela pivot)
     * Relaciona pedidos com produtos e armazena quantidade/preço
     */
    public function up(): void
    {
        Schema::create('pedido_itens', function (Blueprint $table) {
            // ID auto incremento
            $table->id();
            
            // Chave estrangeira para a tabela 'pedidos'
            // onDelete('cascade') = se apagar pedido, apaga itens também
            $table->foreignId('pedido_id')
                  ->constrained()  // Referencia tabela 'pedidos'
                  ->onDelete('cascade');
            
            // Chave estrangeira para a tabela 'produtos'
            $table->foreignId('produto_id')
                  ->constrained()  // Referencia tabela 'produtos'
                  ->onDelete('cascade');
            
            // Quantidade do produto neste pedido
            $table->integer('quantidade');
            
            // Preço unitário na hora da compra
            // Armazenamos aqui porque o preço do produto pode mudar depois
            $table->decimal('preco_unitario', 8, 2);
            
            // Subtotal (quantidade × preço_unitario)
            // Poderíamos calcular, mas armazenamos por performance
            $table->decimal('subtotal', 10, 2);
            
            // Observações específicas deste item (ex: "bem passado")
            $table->text('observacoes_item')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Índice composto para performance em buscas
            // Ajuda quando buscar itens de um pedido específico
            $table->index(['pedido_id', 'produto_id']);
        });
    }

    /**
     * Remove a tabela 'pedido_itens'
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_itens');
    }
};