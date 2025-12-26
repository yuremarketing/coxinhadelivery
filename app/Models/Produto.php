<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    // O 'fillable' diz ao Laravel que esses campos podem ser gravados no banco
    protected $fillable = ['nome', 'preco', 'imagem'];
}
