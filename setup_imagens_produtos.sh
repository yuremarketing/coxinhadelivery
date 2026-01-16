#!/bin/bash

echo "📸 Configurando Sistema de Imagens (Upload e Download)..."

# 1. CRIAR O LINK SIMBÓLICO (Para as imagens aparecerem no navegador)
echo "🔗 Criando link público de storage..."
docker compose exec laravel.test php artisan storage:link

# 2. CRIAR O CONTROLLER DE PRODUTOS PARA ADMIN
cat << 'EOF' > app/Http/Controllers/AdminProdutoController.php
<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdminProdutoController extends Controller
{
    // Cadastrar novo produto com Imagem (Local ou Internet)
    public function store(Request $request)
    {
        // Apenas admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['erro' => 'Não autorizado'], 403);
        }

        $request->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
            'arquivo' => 'nullable|image', // Upload local
            'url_internet' => 'nullable|url' // Link da internet
        ]);

        $caminhoImagem = null;

        // CENÁRIO 1: O usuário enviou um arquivo do computador
        if ($request->hasFile('arquivo')) {
            $caminhoImagem = $request->file('arquivo')->store('produtos', 'public');
        }
        // CENÁRIO 2: O usuário mandou uma URL da internet (Mágica!)
        elseif ($request->filled('url_internet')) {
            try {
                $url = $request->url_internet;
                $conteudo = Http::get($url)->body();
                
                // Gera um nome aleatório
                $nomeArquivo = 'produtos/' . Str::random(20) . '.jpg';
                
                // Salva no disco do servidor
                Storage::disk('public')->put($nomeArquivo, $conteudo);
                $caminhoImagem = $nomeArquivo;
                
            } catch (\Exception $e) {
                return response()->json(['erro' => 'Não consegui baixar a imagem dessa URL.'], 400);
            }
        }

        // Cria o produto no banco
        $produto = Produto::create([
            'nome' => $request->nome,
            'preco' => $request->preco,
            'imagem' => $caminhoImagem
        ]);

        return response()->json([
            'message' => 'Produto criado com sucesso!',
            'produto' => $produto,
            'imagem_url' => $caminhoImagem ? url("storage/".$caminhoImagem) : null
        ], 201);
    }
}
EOF

# 3. REGISTRAR A ROTA
if ! grep -q "/admin/produtos" routes/api.php; then
    echo "" >> routes/api.php
    echo "// Rota para criar produto com imagem" >> routes/api.php
    echo "Route::middleware(['auth:sanctum'])->post('/admin/produtos', [\App\Http\Controllers\AdminProdutoController::class, 'store']);" >> routes/api.php
    echo "✅ Rota de criação de produtos adicionada."
fi

# 4. TESTAR A "MÁGICA" (Baixar da Internet)
echo "---------------------------------------------------"
echo "🧪 Testando cadastro automático via URL..."

# Pegamos o Token do Admin de novo
LOGIN_JSON=$(docker compose exec laravel.test curl -s -X POST http://localhost/api/login \
    -H "Content-Type: application/json" \
    -d '{"email": "admin@coxinha.com", "password": "password"}')
TOKEN=$(echo $LOGIN_JSON | python3 -c "import sys, json; print(json.load(sys.stdin)['access_token'])")

# Vamos tentar cadastrar uma "Coxinha Premium" usando uma foto da Wikipédia
echo "Tentando baixar foto da internet e cadastrar produto..."

docker compose exec laravel.test curl -s -X POST http://localhost/api/admin/produtos \
    -H "Authorization: Bearer $TOKEN" \
    -H "Content-Type: application/json" \
    -d '{
        "nome": "Coxinha Premium da Internet",
        "preco": 8.90,
        "url_internet": "https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Coxinha_de_frango.jpg/640px-Coxinha_de_frango.jpg"
    }' | python3 -m json.tool

echo "---------------------------------------------------"
echo "✅ Se apareceu um caminho em 'imagem', o sistema baixou e salvou a foto sozinho!"
