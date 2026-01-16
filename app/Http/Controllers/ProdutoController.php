<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    public function index()
    {
        // Retorna todos os produtos em formato JSON
        return response()->json(Product::all());
    }
}
