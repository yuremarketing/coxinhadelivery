<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_pedido')->unique()->nullable();
            $table->string('cliente_nome');
            $table->string('cliente_telefone');
            $table->decimal('valor_total', 10, 2)->default(0);
            $table->string('status')->default('pendente');
            $table->string('tipo')->default('entrega');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
