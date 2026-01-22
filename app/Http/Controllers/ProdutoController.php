<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    // 1. Mostrar a lista de produtos (Admin)
    public function index()
    {
         = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

    // 2. Mostrar o formulário de cadastro
    public function create()
    {
        return view('produtos.create');
    }

    // 3. Salvar no Banco
    public function store(Request )
    {
        // Validação
        ->validate([
            'nome' => 'required',
            'preco' => 'required|numeric',
            'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

         = ->all();

        // Upload da Imagem
        if (->hasFile('imagem')) {
             = ->file('imagem')->store('uploads/produtos', 'public');
            ['imagem'] = ;
        }

        Produto::create();

        return redirect()->route('produtos.index')->with('success', 'Produto criado com sucesso!');
    }
}
