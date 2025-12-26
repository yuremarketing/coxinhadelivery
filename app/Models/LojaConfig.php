<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LojaConfig extends Model
{
    use HasFactory;

    /**
     * O nome da tabela no banco. 
     * O Laravel até adivinha sozinho, mas eu gosto de deixar explícito 
     * pra não ter confusão se um dia eu mudar o nome da classe.
     */
    protected $table = 'loja_configs';

    /**
     * Os campos que eu deixo o sistema alterar.
     * Basicamente: o link da live, se a porta tá aberta e quanto tempo 
     * o cliente vai ter que roer a unha esperando.
     */
    protected $fillable = [
        'link_youtube',
        'loja_aberta',
        'tempo_espera_minutos'
    ];

    /**
     * Os tipos dos dados.
     * O 'loja_aberta' é boolean (verdadeiro/falso), não quero "sim" ou "não" escrito.
     * O Laravel já converte isso sozinho pra mim.
     */
    protected $casts = [
        'loja_aberta' => 'boolean',
        'tempo_espera_minutos' => 'integer',
    ];
}
