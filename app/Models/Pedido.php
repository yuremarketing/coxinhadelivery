<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $fillable = ['cliente_nome', 'cliente_telefone', 'status', 'tipo', 'valor_total', 'numero_pedido'];

    protected static function booted()
    {
        static::creating(function ($pedido) {
            if (!$pedido->numero_pedido) {
                $pedido->numero_pedido = 'CX' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }
}
