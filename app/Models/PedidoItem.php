<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_itens';

    protected $fillable = [
        'pedido_id', 
        'produto_id', 
        'quantidade', 
        'preco_unitario', 
        'subtotal', 
        'observacoes_item'
    ];

    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantidade' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            if (empty($item->subtotal) && $item->preco_unitario && $item->quantidade) {
                $item->subtotal = $item->preco_unitario * $item->quantidade;
            }
        });

        static::created(function ($item) {
            if ($item->produto) {
                $item->produto->diminuirEstoque($item->quantidade);
            }
        });

        static::deleted(function ($item) {
            if ($item->produto) {
                $item->produto->estoque += $item->quantidade;
                $item->produto->disponivel = true;
                $item->produto->save();
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

    public function calcularSubtotal(): float
    {
        return $this->preco_unitario * $this->quantidade;
    }

    public function produtoDisponivel(): bool
    {
        return $this->produto && 
               $this->produto->estoque >= $this->quantidade && 
               $this->produto->disponivel;
    }

    public function observacoesFormatadas(): string
    {
        if (empty($this->observacoes_item)) return 'Sem observações';
        return ucfirst(strtolower(trim($this->observacoes_item)));
    }
}
