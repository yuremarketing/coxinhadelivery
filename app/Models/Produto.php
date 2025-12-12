<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model Produto
 * Representa um produto no sistema (ex: coxinha, refrigerante)
 * 
 * @property int $id
 * @property string $nome
 * @property string|null $descricao
 * @property float $preco
 * @property string $categoria
 * @property string|null $imagem
 * @property bool $disponivel
 * @property int $estoque
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Produto extends Model
{
    use HasFactory;

    /**
     * Nome da tabela no banco de dados
     * Laravel assume automaticamente "produtos" (plural do nome da classe)
     * PONTO DE ESSTUDO, SABER A DIFERENÇA DE MODEL PRA TABELA, muda o jogo... vamos nessa!!*/
    protected $table = 'produtos';

    /**
     * Campos que podem ser preenchidos em massa (mass assignment)
     * Ex: Produto::create([...]) ou $produto->update([...])
     * Segurança: só esses campos podem ser setados assim
     */
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'categoria',
        'imagem',
        'disponivel',
        'estoque'
    ];

    /**
     * Tipagem dos campos (casting)
     * Converte automaticamente tipos do banco para PHP
     */
    protected $casts = [
        'preco' => 'decimal:2',  // Converte string para float com 2 decimais
        'disponivel' => 'boolean', // Converte 0/1 para false/true
        'estoque' => 'integer'    // Converte para int
    ];

    /**
     * Relacionamento: Um produto pode estar em muitos itens de pedido
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pedidoItens()
    {
        return $this->hasMany(PedidoItem::class, 'produto_id');
    }

    /**
     * Scope para filtrar produtos disponíveis
     * Ex: Produto::disponiveis()->get()
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('disponivel', true)->where('estoque', '>', 0);
    }

    /**
     * Scope para filtrar por categoria
     * Ex: Produto::porCategoria('salgados')->get()
     */
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Verifica se o produto está disponível para venda
     */
    public function estaDisponivel(): bool
    {
        return $this->disponivel && $this->estoque > 0;
    }

    /**
     * Diminui o estoque do produto
     */
    public function diminuirEstoque(int $quantidade): bool
    {
        if ($this->estoque >= $quantidade) {
            $this->estoque -= $quantidade;
            // Se estoque zerou, marca como indisponível
            if ($this->estoque === 0) {
                $this->disponivel = false;
            }
            return $this->save();
        }
        return false;
    }
}