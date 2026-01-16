<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class VenderController extends Controller
{
    public function index()
    {
        // Busca os produtos para mostrar na tela do celular
        $produtos = Product::all();
        return view('vender', compact('produtos'));
    }
}
