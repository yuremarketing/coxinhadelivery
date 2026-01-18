<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
{
    use HasFactory;

    protected $table = 'pedido_itens'; // Garante que o Laravel ache a tabela certa

    protected $fillable = [
        'pedido_id',
        'produto_id',
        'quantidade',
        'preco_unitario' // Liberando o preço também
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
