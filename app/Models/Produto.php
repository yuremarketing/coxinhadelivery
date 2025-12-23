<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'preco', 'categoria', 'estoque', 'disponivel', 'imagem'];

    protected function imagemUrl(): Attribute
    {
        return Attribute::get(function () {
            if (!$this->imagem) return null;
            // Retorna o link completo usando a função asset()
            return asset($this->imagem);
        });
    }

    protected $appends = ['imagem_url'];
}
