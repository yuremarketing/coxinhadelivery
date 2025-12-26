<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutosSeeder extends Seeder
{
    public function run(): void
    {
        // Limpa a tabela antes de inserir pra não duplicar no dev
        // Produto::truncate(); 

        Produto::create([
            'nome' => 'Coxinha de Frango',
            'preco' => 8.50,
            'descricao' => 'A clássica, imbatível.',
            'ativo' => true
        ]);

        Produto::create([
            'nome' => 'Coxinha de Costela',
            'preco' => 12.00,
            'descricao' => 'Recheio premium.',
            'ativo' => true
        ]);

        Produto::create([
            'nome' => 'Coca-Cola Lata',
            'preco' => 6.00,
            'descricao' => '350ml gelada.',
            'ativo' => true
        ]);
    }
}
