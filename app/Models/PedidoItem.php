<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    protected $table = 'pedido_itens';
    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario', 'subtotal'];

    protected static function booted()
    {
        static::creating(function ($item) {
            if (!$item->subtotal) {
                $item->subtotal = $item->quantidade * $item->preco_unitario;
            }
        });
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
