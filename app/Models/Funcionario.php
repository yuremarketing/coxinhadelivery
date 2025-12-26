<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;

    // Garante que o Laravel olhe para a tabela certa
    protected $table = 'funcionarios';

    // Lista de campos que eu permito salvar em massa (Segurança)
    protected $fillable = [
        'nome',
        'telefone',
        'cargo',
        'placa_veiculo',
        'modelo_veiculo',
        'foto_url',
        'ativo'
    ];
}
