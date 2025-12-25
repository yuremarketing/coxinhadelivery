<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'categoria',    // Adicionei aqui
        'quantidade',   // Adicionei aqui
        'imagem_url'    // Adicionei aqui
    ];
}
