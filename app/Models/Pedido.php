<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero_pedido',
        'cliente_nome',
        'cliente_telefone',
        'cliente_email',
        'status',
        'tipo',
        'valor_total',
        'observacoes'
    ];

    protected static function boot()
    {
        parent::boot();

        // Gera o número do pedido automaticamente antes de criar (Ex: CX20251223ABCD)
        static::creating(function ($pedido) {
            $pedido->numero_pedido = 'CX' . date('Ymd') . strtoupper(Str::random(4));
        });
    }

    /**
     * Relacionamento: Um pedido tem muitos itens
     */
    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }

    /**
     * NOVO: Método para atualizar o status do pedido
     */
    public function atualizarStatus(string $novoStatus): bool
    {
        $this->status = $novoStatus;
        return $this->save();
    }

    /**
     * NOVO: Verifica se o pedido pode ser cancelado
     */
    public function podeSerCancelado(): bool
    {
        // Regra de negócio: Só cancela se não tiver saído para entrega/pronto
        return in_array($this->status, ['pendente', 'confirmado']);
    }
}
