<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * Model Pedido
 * Representa um pedido feito por um cliente
 * 
 * @property int $id
 * @property string $numero_pedido
 * @property string $cliente_nome
 * @property string $cliente_telefone
 * @property string|null $cliente_email
 * @property string $status
 * @property string $tipo
 * @property string|null $observacoes
 * @property float $valor_total
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Pedido extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     */
    protected $table = 'pedidos';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'numero_pedido',
        'cliente_nome',
        'cliente_telefone',
        'cliente_email',
        'status',
        'tipo',
        'observacoes',
        'valor_total'
    ];

    /**
     * Tipagem dos campos
     */
    protected $casts = [
        'valor_total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Status possíveis para um pedido
     */
    public const STATUS = [
        'pendente' => 'Pendente',
        'confirmado' => 'Confirmado',
        'em_preparo' => 'Em Preparo',
        'pronto' => 'Pronto',
        'entregue' => 'Entregue',
        'cancelado' => 'Cancelado'
    ];

    /**
     * Tipos possíveis de pedido
     */
    public const TIPOS = [
        'retirada' => 'Retirada',
        'entrega' => 'Entrega'
    ];

    /**
     * Boot do model - Executa ações quando o model é inicializado
     */
    protected static function boot()
    {
        parent::boot();

        // Gera número do pedido automaticamente antes de criar
        static::creating(function ($pedido) {
            if (empty($pedido->numero_pedido)) {
                $pedido->numero_pedido = self::gerarNumeroPedido();
            }
        });
    }

    /**
     * Relacionamento: Um pedido tem muitos itens
     */
    public function itens()
    {
        return $this->hasMany(PedidoItem::class, 'pedido_id');
    }

    /**
     * Relacionamento através dos itens: Um pedido tem muitos produtos
     */
    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pedido_itens', 'pedido_id', 'produto_id')
                    ->withPivot('quantidade', 'preco_unitario', 'subtotal', 'observacoes_item')
                    ->withTimestamps();
    }

    /**
     * Gera um número único para o pedido
     * Formato: CX + ano + mês + dia + 4 dígitos aleatórios
     * Ex: CX202412120015
     */
    public static function gerarNumeroPedido(): string
    {
        $prefixo = 'CX' . date('Ymd');
        $ultimoPedido = self::where('numero_pedido', 'like', $prefixo . '%')
                            ->orderBy('numero_pedido', 'desc')
                            ->first();

        if ($ultimoPedido) {
            $ultimoNumero = (int) substr($ultimoPedido->numero_pedido, -4);
            $novoNumero = str_pad($ultimoNumero + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $novoNumero = '0001';
        }

        return $prefixo . $novoNumero;
    }

    /**
     * Atualiza o status do pedido
     */
    public function atualizarStatus(string $novoStatus): bool
    {
        if (array_key_exists($novoStatus, self::STATUS)) {
            $this->status = $novoStatus;
            return $this->save();
        }
        return false;
    }

    /**
     * Verifica se o pedido pode ser cancelado
     * (só pode cancelar se não estiver pronto ou entregue)
     */
    public function podeSerCancelado(): bool
    {
        return !in_array($this->status, ['pronto', 'entregue']);
    }

    /**
     * Calcula o valor total do pedido baseado nos itens
     */
    public function calcularTotal(): float
    {
        return $this->itens->sum('subtotal');
    }

    /**
     * Scope para pedidos pendentes
     */
    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    /**
     * Scope para pedidos de hoje
     */
    public function scopeDeHoje($query)
    {
        return $query->whereDate('created_at', today());
    }
}