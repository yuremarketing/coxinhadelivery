<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PedidoItem extends Model
    protected $table = "pedido_items";
{
    use HasFactory;
    protected $table = 'pedido_itens';
    protected $fillable = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario'];
    public function pedido() { return $this->belongsTo(Order::class); }
    public function produto() { return $this->belongsTo(Product::class); }
}
