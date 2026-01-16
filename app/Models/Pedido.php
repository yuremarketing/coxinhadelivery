<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';
    protected $fillable = ['cliente_nome', 'cliente_telefone', 'status', 'tipo', 'valor_total', 'numero_pedido'];

    const STATUS = [
        'pendente' => 'Pendente',
        'em_preparo' => 'Em Preparo',
        'pronto' => 'Pronto',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado'
    ];

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

    public function calcularTotal()
    {
        $this->valor_total = $this->itens->sum('subtotal');
        $this->save();
        return $this->valor_total;
    }
}
