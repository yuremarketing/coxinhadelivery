<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Produto;
use Tests\TestCase;

class ProdutoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_criar_um_produto()
    {
        // 1. Arrange: Preparar dados
        $dados = [
            'nome' => 'Coxinha de Frango',
            'descricao' => 'Coxinha tradicional com frango desfiado',
            'preco' => 5.50,
            'categoria' => 'coxinhas',
            'imagem' => 'coxinha_frango.jpg',
            'estoque' => 50,
            'disponivel' => true
        ];

        // 2. Act: Executar ação
        $produto = Produto::create($dados);

        // 3. Assert: Verificar resultado
        $this->assertInstanceOf(Produto::class, $produto);
        $this->assertEquals('Coxinha de Frango', $produto->nome);
        $this->assertEquals(5.50, $produto->preco);
        $this->assertEquals('coxinhas', $produto->categoria);
        $this->assertTrue($produto->disponivel);
    }

    /** @test */
    public function scope_disponiveis_filtra_apenas_produtos_disponiveis()
    {
        // Criar produtos com diferentes estados
        $produto1 = Produto::create([
            'nome' => 'Coxinha Disponível',
            'descricao' => 'Produto disponível',
            'preco' => 5.00,
            'categoria' => 'coxinhas',
            'estoque' => 10,
            'disponivel' => true
        ]);

        $produto2 = Produto::create([
            'nome' => 'Coxinha Sem Estoque',
            'descricao' => 'Produto sem estoque',
            'preco' => 5.00,
            'categoria' => 'coxinhas',
            'estoque' => 0,  // Estoque ZERO
            'disponivel' => true
        ]);

        $produto3 = Produto::create([
            'nome' => 'Coxinha Indisponível',
            'descricao' => 'Produto marcado como indisponível',
            'preco' => 5.00,
            'categoria' => 'coxinhas',
            'estoque' => 10,
            'disponivel' => false  // Marcado como indisponível
        ]);

        // Executar scope disponiveis()
        $produtosDisponiveis = Produto::disponiveis()->get();

        // Verificar resultados
        $this->assertCount(1, $produtosDisponiveis); // Apenas 1 produto deve estar disponível
        $this->assertTrue($produtosDisponiveis->contains($produto1)); // produto1 deve estar
        $this->assertFalse($produtosDisponiveis->contains($produto2)); // produto2 NÃO deve estar (estoque 0)
        $this->assertFalse($produtosDisponiveis->contains($produto3)); // produto3 NÃO deve estar (disponivel = false)
    }
}
