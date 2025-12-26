<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique(); // Ex: CX2024...
            
            // Cliente
            $table->string('cliente_nome');
            $table->string('cliente_telefone');
            
            // Status e Tipo
            $table->enum('status', ['pendente', 'confirmado', 'em_preparo', 'pronto', 'entregue', 'cancelado'])->default('pendente');
            $table->enum('tipo', ['retirada', 'entrega'])->default('retirada');
            
            // Financeiro
            $table->decimal('valor_total', 10, 2);
            
            // Vínculo com Entregador (Já incluso aqui pra não dar erro depois)
            $table->foreignId('entregador_id')->nullable()->constrained('funcionarios');
            
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('pedidos'); }
};
