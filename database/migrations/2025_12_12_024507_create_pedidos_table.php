<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela 'pedidos' no banco de dados
     */
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            // ID auto incremento
            $table->id();
            
            // Número único do pedido (ex: CX202412120001)
            // unique() = não pode repetir
            $table->string('numero_pedido')->unique();
            
            // Dados do cliente (poderia ser chave estrangeira para users, 
            // mas mantemos simples por enquanto)
            $table->string('cliente_nome');
            $table->string('cliente_telefone');
            $table->string('cliente_email')->nullable();
            
            // Status do pedido (enum = valor deve ser um desses)
            $table->enum('status', [
                'pendente',      // Cliente fez pedido, não confirmado
                'confirmado',    // Loja confirmou recebimento
                'em_preparo',    // Cozinha preparando
                'pronto',        // Pronto para retirada/entrega
                'entregue',      // Entregue ao cliente
                'cancelado'      // Pedido cancelado
            ])->default('pendente');
            
            // Tipo: retirada na loja ou entrega
            $table->enum('tipo', ['retirada', 'entrega'])->default('retirada');
            
            // Observações do cliente (ex: "sem cebola")
            $table->text('observacoes')->nullable();
            
            // Valor total do pedido (soma de todos itens)
            $table->decimal('valor_total', 10, 2);
            
            // created_at e updated_at automáticos
            $table->timestamps();
        });
    }

    /**
     * Remove a tabela 'pedidos' do banco de dados
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};