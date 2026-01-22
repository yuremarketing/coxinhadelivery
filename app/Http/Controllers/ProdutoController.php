<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProdutoController extends Controller
{
    // 1. Listar
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

    // 2. Tela de Criar
    public function create()
    {
        return view('produtos.create');
    }

    // 3. Salvar Novo
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $dados = $request->all();

        if ($request->hasFile('imagem')) {
            $dados['imagem'] = $request->file('imagem')->store('uploads/produtos', 'public');
        }

        Produto::create($dados);
        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }

    // 4. Tela de Editar (NOVO)
    public function edit($id)
    {
        $produto = Produto::findOrFail($id);
        return view('produtos.edit', compact('produto'));
    }

    // 5. Atualizar no Banco (NOVO)
    public function update(Request $request, $id)
    {
        $produto = Produto::findOrFail($id);

        $request->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
        ]);

        $dados = $request->all();

        // Se enviou nova imagem, apaga a velha e sobe a nova
        if ($request->hasFile('imagem')) {
            if ($produto->imagem) {
                Storage::disk('public')->delete($produto->imagem);
            }
            $dados['imagem'] = $request->file('imagem')->store('uploads/produtos', 'public');
        }

        $produto->update($dados);
        return redirect()->route('produtos.index')->with('success', 'Produto atualizado!');
    }

    // 6. Excluir (NOVO)
    public function destroy($id)
    {
        $produto = Produto::findOrFail($id);
        
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();
        return redirect()->route('produtos.index')->with('success', 'Produto excluído!');
    }
}
