<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProdutosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Popula a tabela 'produtos' com dados de exemplo
     */
    public function run(): void
    {
        /**Primeiro, limpa a tabela (opcional, mas bom para desenvolvimento). Mas ao "rodar" via que não
         * podemos truncar a tabela produtos porque tem chave estrangeira na tabela pedido_itens, 
         * vamos Desabilita verificação de foreign keys
         *  temporariamente.
        //DB::table('produtos')->truncate();
        */

        // Desabilita verificação de foreign keys temporariamente, 
         DB::statement('SET FOREIGN_KEY_CHECKS=0;');
         DB::table('produtos')->truncate();
         DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Array com os produtos da lanchonete
        $produtos = [
            // COXINHAS TRADICIONAIS
            [
                'nome' => 'Coxinha de Frango',
                'descricao' => 'Coxinha tradicional com frango desfiado temperado',
                'preco' => 5.50,
                'categoria' => 'coxinhas',
                'imagem' => 'coxinha_frango.jpg',
                'estoque' => 50,
                'disponivel' => true,
            ],
            [
                'nome' => 'Coxinha de Frango com Catupiry',
                'descricao' => 'Coxinha recheada com frango e catupiry cremoso',
                'preco' => 6.50,
                'categoria' => 'coxinhas',
                'imagem' => 'coxinha_catupiry.jpg',
                'estoque' => 30,
                'disponivel' => true,
            ],
            [
                'nome' => 'Coxinha de Carne',
                'descricao' => 'Coxinha com recheio de carne moída temperada',
                'preco' => 5.50,
                'categoria' => 'coxinhas',
                'imagem' => 'coxinha_carne.jpg',
                'estoque' => 40,
                'disponivel' => true,
            ],
            [
                'nome' => 'Coxinha de Calabresa',
                'descricao' => 'Coxinha com calabresa e queijo mussarela',
                'preco' => 6.00,
                'categoria' => 'coxinhas',
                'imagem' => 'coxinha_calabresa.jpg',
                'estoque' => 25,
                'disponivel' => true,
            ],
            [
                'nome' => 'Coxinha de Camarão',
                'descricao' => 'Coxinha especial com recheio de camarão',
                'preco' => 7.50,
                'categoria' => 'coxinhas',
                'preco' => 7.50,
                'categoria' => 'coxinhas',
                'imagem' => 'coxinha_camarao.jpg',
                'estoque' => 15,
                'disponivel' => true,
            ],

            // SAVOURIES (SALGADOS DIVERSOS)
            [
                'nome' => 'Pastel de Carne',
                'descricao' => 'Pastel frito com recheio de carne',
                'preco' => 6.00,
                'categoria' => 'salgados',
                'imagem' => 'pastel_carne.jpg',
                'estoque' => 35,
                'disponivel' => true,
            ],
            [
                'nome' => 'Empada de Frango',
                'descricao' => 'Empada assada com frango',
                'preco' => 4.50,
                'categoria' => 'salgados',
                'imagem' => 'empada_frango.jpg',
                'estoque' => 40,
                'disponivel' => true,
            ],
            [
                'nome' => 'Enroladinho de Salsicha',
                'descricao' => 'Massa crocante enrolada na salsicha',
                'preco' => 4.00,
                'categoria' => 'salgados',
                'imagem' => 'enroladinho.jpg',
                'estoque' => 60,
                'disponivel' => true,
            ],
            [
                'nome' => 'Kibe',
                'descricao' => 'Kibe frito tradicional',
                'preco' => 5.00,
                'categoria' => 'salgados',
                'imagem' => 'kibe.jpg',
                'estoque' => 45,
                'disponivel' => true,
            ],

            // OPÇÕES VEGANAS
            [
                'nome' => 'Coxinha Vegana',
                'descricao' => 'Coxinha com proteína de soja e legumes',
                'preco' => 6.50,
                'categoria' => 'veganos',
                'imagem' => 'coxinha_vegana.jpg',
                'estoque' => 20,
                'disponivel' => true,
            ],
            [
                'nome' => 'Pastel Vegano',
                'descricao' => 'Pastel com recheio de palmito e espinafre',
                'preco' => 6.50,
                'categoria' => 'veganos',
                'imagem' => 'pastel_vegano.jpg',
                'estoque' => 25,
                'disponivel' => true,
            ],

            // BEBIDAS
            [
                'nome' => 'Coca-Cola 350ml',
                'descricao' => 'Refrigerante Coca-Cola lata',
                'preco' => 4.50,
                'categoria' => 'bebidas',
                'imagem' => 'coca_lata.jpg',
                'estoque' => 100,
                'disponivel' => true,
            ],
            [
                'nome' => 'Guaraná Antarctica 350ml',
                'descricao' => 'Refrigerante Guaraná lata',
                'preco' => 4.50,
                'categoria' => 'bebidas',
                'imagem' => 'guarana_lata.jpg',
                'estoque' => 80,
                'disponivel' => true,
            ],
            [
                'nome' => 'Suco de Laranja 500ml',
                'descricao' => 'Suco de laranja natural',
                'preco' => 7.00,
                'categoria' => 'bebidas',
                'imagem' => 'suco_laranja.jpg',
                'estoque' => 50,
                'disponivel' => true,
            ],
            [
                'nome' => 'Água Mineral 500ml',
                'descricao' => 'Água sem gás',
                'preco' => 3.00,
                'categoria' => 'bebidas',
                'imagem' => 'agua.jpg',
                'estoque' => 120,
                'disponivel' => true,
            ],
            [
                'nome' => 'Café Expresso',
                'descricao' => 'Café expresso tradicional',
                'preco' => 3.50,
                'categoria' => 'bebidas',
                'imagem' => 'cafe.jpg',
                'estoque' => 200,
                'disponivel' => true,
            ],

            // SOBREMESAS
            [
                'nome' => 'Brigadeiro',
                'descricao' => 'Doce de chocolate granulado',
                'preco' => 3.50,
                'categoria' => 'sobremesas',
                'imagem' => 'brigadeiro.jpg',
                'estoque' => 60,
                'disponivel' => true,
            ],
            [
                'nome' => 'Beijinho',
                'descricao' => 'Doce de coco com leite condensado',
                'preco' => 3.50,
                'categoria' => 'sobremesas',
                'imagem' => 'beijinho.jpg',
                'estoque' => 50,
                'disponivel' => true,
            ],
            [
                'nome' => 'Pudim',
                'descricao' => 'Pudim de leite condensado com calda de caramelo',
                'preco' => 8.00,
                'categoria' => 'sobremesas',
                'imagem' => 'pudim.jpg',
                'estoque' => 15,
                'disponivel' => true,
            ],
            [
                'nome' => 'Brownie',
                'descricao' => 'Brownie de chocolate com nozes',
                'preco' => 6.50,
                'categoria' => 'sobremesas',
                'imagem' => 'brownie.jpg',
                'estoque' => 30,
                'disponivel' => true,
            ],
        ];

        // Insere cada produto no banco
        foreach ($produtos as $produtoData) {
            Produto::create($produtoData);
        }

        // Mensagem de sucesso
        $this->command->info(count($produtos) . ' produtos cadastrados com sucesso!');
        $this->command->info('Categorias disponíveis: coxinhas, salgados, veganos, bebidas, sobremesas');
    }
}