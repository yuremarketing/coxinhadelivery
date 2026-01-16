<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = ['cliente_nome', 'cliente_telefone', 'status', 'tipo', 'valor_total', 'numero_pedido'];
}
