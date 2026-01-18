<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        // Agora busca 'Produto' (PT) que existe, e não 'Product' (EN)
        return response()->json(Produto::all());
    }
}
