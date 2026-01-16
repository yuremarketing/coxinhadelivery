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
            // Calcula o subtotal se não vier preenchido
            if (!$item->subtotal) {
                $item->subtotal = $item->quantidade * $item->preco_unitario;
            }

            // Lógica de baixar o estoque que o teste exige
            if ($item->produto) {
                $item->produto->decrement('estoque', $item->quantidade);
            }
        });
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
