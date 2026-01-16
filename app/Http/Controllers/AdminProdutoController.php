<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AdminProductController extends Controller
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
        $produto = Product::create([
            'nome' => $request->nome,
            'preco' => $request->preco,
            'imagem' => $caminhoImagem
        ]);

        return response()->json([
            'message' => 'Product criado com sucesso!',
            'produto' => $produto,
            'imagem_url' => $caminhoImagem ? url("storage/".$caminhoImagem) : null
        ], 201);
    }
}
