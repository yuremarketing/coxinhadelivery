<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ProdutoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validador = Validator::make($request->all(), [
                'nome' => 'required|string|max:100',
                'preco' => 'required|numeric',
                'categoria' => 'required|string',
                'estoque' => 'required|integer',
                'imagem' => 'nullable|image|max:5120'
            ]);

            if ($validador->fails()) {
                return response()->json(['success' => false, 'errors' => $validador->errors()], 422);
            }

            $dados = $validador->validated();

            if ($request->hasFile('imagem')) {
                $arquivo = $request->file('imagem');
                $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
                
                // MOVE O ARQUIVO PARA public/uploads/produtos
                $arquivo->move(public_path('uploads/produtos'), $nomeArquivo);
                
                // SALVA O CAMINHO QUE O NAVEGADOR VAI USAR
                $dados['imagem'] = 'uploads/produtos/' . $nomeArquivo;
            }

            $produto = Produto::create($dados);

            return response()->json([
                'success' => true,
                'message' => 'Produto criado com sucesso',
                'data' => $produto
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
