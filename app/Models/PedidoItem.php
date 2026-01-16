<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';
    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario', 'subtotal'];
}
