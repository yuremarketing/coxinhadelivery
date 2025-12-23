<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome', 'descricao', 'preco', 'categoria', 'estoque', 'disponivel', 'imagem'
    ];

    // O FISCAL ENTRA AQUI:
    // Isso garante que o Laravel converta os valores vindo do banco para os tipos certos
    protected $casts = [
        'preco' => 'double',
        'estoque' => 'integer',
        'disponivel' => 'boolean',
    ];

    protected function imagemUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->imagem) return null;
            if (filter_var($this->imagem, FILTER_VALIDATE_URL)) return $this->imagem;
            return asset($this->imagem);
        });
    }

    protected $appends = ['imagem_url'];
}
