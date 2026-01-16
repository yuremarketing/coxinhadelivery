<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('produtos')->insert([
            ['nome' => 'Coxinha Tradicional', 'preco' => 5.50, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Coxinha Catupiry', 'preco' => 6.50, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Kibe com Queijo', 'preco' => 6.00, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()],
            ['nome' => 'Coca-Cola Lata', 'preco' => 5.00, 'imagem' => null, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
