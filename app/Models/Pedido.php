<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    // A LISTA VIP: Só o que está aqui entra no banco
    protected $fillable = [
        'user_id',
        'total',          // <--- O Porteiro agora vai deixar o total entrar!
        'status',
        'numero_pedido'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }
}
