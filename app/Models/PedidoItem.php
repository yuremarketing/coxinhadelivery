<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model PedidoItem
 * Representa um item dentro de um pedido (tabela pivot)
 * 
 * @property int $id
 * @property int $pedido_id
 * @property int $produto_id
 * @property int $quantidade
 * @property float $preco_unitario
 * @property float $subtotal
 * @property string|null $observacoes_item
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class PedidoItem extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     * 
     */
    protected $table = 'pedido_itens';

    /**
     * Campos que podem ser preenchidos em massa
     */
    protected $fillable = [
        'pedido_id',
        'produto_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
        'observacoes_item'
    ];

    /**
     * Tipagem dos campos
     */
    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'quantidade' => 'integer'
    ];

    /**
     * Boot do model
     */
    protected static function boot()
    {
        parent::boot();

        // Calcula subtotal automaticamente antes de salvar
        static::saving(function ($item) {
            if (empty($item->subtotal) && $item->preco_unitario && $item->quantidade) {
                $item->subtotal = $item->preco_unitario * $item->quantidade;
            }
        });

        // Atualiza estoque do produto quando item é criado
        static::created(function ($item) {
            if ($item->produto) {
                $item->produto->diminuirEstoque($item->quantidade);
            }
        });

        // Ajusta estoque se item for deletado
        static::deleted(function ($item) {
            if ($item->produto) {
                $item->produto->estoque += $item->quantidade;
                $item->produto->disponivel = true;
                $item->produto->save();
            }
        });
    }

    /**
     * Relacionamento: Um item pertence a um pedido
     */
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    /**
     * Relacionamento: Um item referencia um produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    /**
     * Calcula o subtotal (quantidade × preço unitário)
     */
    public function calcularSubtotal(): float
    {
        return $this->preco_unitario * $this->quantidade;
    }

    /**
     * Verifica se o produto ainda está disponível na quantidade solicitada
     */
    public function produtoDisponivel(): bool
    {
        return $this->produto && 
               $this->produto->estoque >= $this->quantidade && 
               $this->produto->disponivel;
    }

    /**
     * Formata as observações do item
     * Ex: "Sem cebola, bem passado"
     */
    public function observacoesFormatadas(): string
    {
        if (empty($this->observacoes_item)) {
            return 'Sem observações';
        }

        // Capitaliza primeira letra
        return ucfirst(strtolower(trim($this->observacoes_item)));
    }
}