#!/bin/bash

echo "🚀 Configurando API de Produtos..."

# 1. CRIAR O CONTROLLER
# Define a lógica que busca todos os produtos do banco e retorna como JSON
cat << 'EOF' > app/Http/Controllers/ProdutoController.php
<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        // Retorna todos os produtos em formato JSON
        return response()->json(Produto::all());
    }
}
EOF

# 2. ADICIONAR A ROTA
# Vamos adicionar a rota no final do arquivo api.php
# Usamos o caminho completo da classe para evitar erros de importação
echo "" >> routes/api.php
echo "// Rota criada via script para listar produtos" >> routes/api.php
echo "Route::get('/produtos', [\App\Http\Controllers\ProdutoController::class, 'index']);" >> routes/api.php

# 3. TESTAR A API
echo "🧪 Testando a rota /api/produtos..."
echo "Aguarde a resposta do servidor..."
echo "---------------------------------------------------"

# Faz uma requisição interna no container para ver se devolve o JSON
docker compose exec laravel.test curl -s http://localhost/api/produtos | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se você viu a lista de coxinhas acima, a API está PRONTA!"
