#!/bin/bash

echo "🚀 Iniciando configuração automática do Coxinha Delivery..."

# --- 1. CORREÇÃO DOS ARQUIVOS PHP (MODELS E SEEDERS) ---
echo "📝 Corrigindo arquivos de Model e Seeder..."

# Remove o model errado se existir
rm -f app/Models/ItemPedido.php

# Cria PedidoItem.php correto
cat << 'EOF' > app/Models/PedidoItem.php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;
    protected $table = 'pedido_itens';
    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'];
    public function pedido() { return $this->belongsTo(Pedido::class); }
    public function produto() { return $this->belongsTo(Produto::class); }
}
EOF

# Atualiza Pedido.php com relacionamento
cat << 'EOF' > app/Models/Pedido.php
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'status'];
    public function itens() { return $this->hasMany(PedidoItem::class); }
    public function user() { return $this->belongsTo(User::class); }
}
EOF

# Cria ProdutoSeeder.php
cat << 'EOF' > database/seeders/ProdutoSeeder.php
<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produtos')->insert([
            ['nome' => 'Coxinha Tradicional', 'preco' => 5.50, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Coxinha Catupiry', 'preco' => 6.50, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Kibe com Queijo', 'preco' => 6.00, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Coca-Cola Lata', 'preco' => 5.00, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
EOF

# Atualiza DatabaseSeeder.php
cat << 'EOF' > database/seeders/DatabaseSeeder.php
<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@coxinha.com',
            'password' => bcrypt('password'),
        ]);
        $this->call([ProdutoSeeder::class]);
    }
}
EOF

# --- 2. EXECUÇÃO NO DOCKER ---
echo "🐳 Garantindo que os containers estão de pé..."
docker compose up -d

echo "🔍 Detectando serviço Laravel no Docker..."
# Tenta achar o nome do serviço (geralmente 'laravel.test' ou 'app')
SERVICE_NAME=$(docker compose config --services | grep -E 'laravel.test|app|php' | head -n 1)

if [ -z "$SERVICE_NAME" ]; then
    echo "⚠️ Não consegui detectar o nome do serviço automaticamente. Tentando 'app'..."
    SERVICE_NAME="app"
fi
echo "✅ Usando serviço: $SERVICE_NAME"

echo "♻️  Rodando Migrations e Seeds dentro do container..."
docker compose exec $SERVICE_NAME php artisan migrate:fresh --seed

echo "✅ SUCESSO! Banco recriado e populado."
