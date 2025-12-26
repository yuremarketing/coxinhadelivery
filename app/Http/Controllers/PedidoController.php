<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    public function vender()
    {
        $produtos = Produto::all();
        return view('vender', compact('produtos'));
    }
}
