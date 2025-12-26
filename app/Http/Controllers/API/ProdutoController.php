<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProdutoController extends Controller
{
    public function index(): JsonResponse
    {
        $produtos = Produto::all();
        return response()->json(['success' => true, 'data' => $produtos]);
    }

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
                $arquivo->move(public_path('uploads/produtos'), $nomeArquivo);
                $dados['imagem'] = 'uploads/produtos/' . $nomeArquivo;
            }

            $produto = Produto::create($dados);

            return response()->json(['success' => true, 'message' => 'Produto criado', 'data' => $produto], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $produto = Produto::find($id);
        if (!$produto) return response()->json(['success' => false, 'message' => 'Não encontrado'], 404);

        $dados = $request->all();

        if ($request->hasFile('imagem')) {
            if ($produto->imagem && File::exists(public_path($produto->imagem))) {
                File::delete(public_path($produto->imagem));
            }

            $arquivo = $request->file('imagem');
            $nomeArquivo = time() . '_' . $arquivo->getClientOriginalName();
            $arquivo->move(public_path('uploads/produtos'), $nomeArquivo);
            $dados['imagem'] = 'uploads/produtos/' . $nomeArquivo;
        }

        $produto->update($dados);

        return response()->json(['success' => true, 'message' => 'Produto atualizado', 'data' => $produto]);
    }

    public function destroy($id): JsonResponse
    {
        $produto = Produto::find($id);
        if (!$produto) return response()->json(['success' => false, 'message' => 'Não encontrado'], 404);

        if ($produto->imagem && File::exists(public_path($produto->imagem))) {
            File::delete(public_path($produto->imagem));
        }

        $produto->delete();
        return response()->json(['success' => true, 'message' => 'Produto e imagem removidos']);
    }
}
