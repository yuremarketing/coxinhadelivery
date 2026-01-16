#!/bin/bash

echo "🔧 Corrigindo a tabela de Pedidos..."

# 1. REESCREVER A MIGRATION DE PEDIDOS
# Adicionando o campo foreignId('user_id') que estava faltando
cat << 'EOF' > database/migrations/2025_01_01_000003_create_pedidos_table.php
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
            // Aqui está a correção: criando o vínculo com o usuário
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('pendente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
EOF

# 2. RESETAR O BANCO DE DADOS
# Precisamos rodar de novo para a nova coluna aparecer
echo "♻️  Recriando o banco com a nova estrutura..."
docker compose exec laravel.test php artisan migrate:fresh --seed

# 3. TESTAR A VENDA NOVAMENTE (POST)
echo "---------------------------------------------------"
echo "🧪 Tentando vender a Coxinha novamente..."

docker compose exec laravel.test curl -s -X POST http://localhost/api/pedidos \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -d '{
        "itens": [
            {"produto_id": 1, "quantidade": 2, "preco_unitario": 5.50},
            {"produto_id": 4, "quantidade": 1, "preco_unitario": 5.00}
        ]
    }' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se você ver o JSON do pedido criado, PROBLEMA RESOLVIDO!"
