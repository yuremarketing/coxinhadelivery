<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_pedido',
        'cliente_nome',
        'cliente_telefone',
        'tipo',
        'valor_total',
        'status',
        'observacoes',
    ];

    // COMENTÁRIO: Isso aqui gera o número do pedido automaticamente antes de salvar no banco
    protected static function booted()
    {
        static::creating(function ($pedido) {
            $ultimoPedido = self::latest()->first();
            $proximoNumero = $ultimoPedido ? ($ultimoPedido->id + 1) : 1;
            $pedido->numero_pedido = 'PED-' . str_pad($proximoNumero, 5, '0', STR_PAD_LEFT);
        });
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
