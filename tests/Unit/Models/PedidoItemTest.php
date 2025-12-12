<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_criar_um_item_de_pedido()
    {
        $produto = Produto::create([
            'nome' => 'Coxinha de Frango',
            'descricao' => 'Coxinha tradicional',
            'preco' => 5.50,
            'estoque' => 10,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'João Silva',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 5.50,
            'subtotal' => 11.00,
        ]);

        $this->assertNotNull($item);
        $this->assertEquals($pedido->id, $item->pedido_id);
        $this->assertEquals($produto->id, $item->produto_id);
        $this->assertEquals(2, $item->quantidade);
        $this->assertEquals(5.50, $item->preco_unitario);
        $this->assertEquals(11.00, $item->subtotal);
    }

    /** @test */
    public function calcula_subtotal_automaticamente_se_nao_fornecido()
    {
        $produto = Produto::create([
            'nome' => 'Refrigerante',
            'preco' => 4.00,
            'estoque' => 20,
            'disponivel' => true,
            'categoria' => 'bebidas',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Teste',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 3,
            'preco_unitario' => 4.00,
            // subtotal não fornecido, deve ser calculado
        ]);

        // Esperado: 3 * 4.00 = 12.00
        $this->assertEquals(12.00, $item->subtotal);
    }

    /** @test */
    public function pertence_a_um_pedido()
    {
        $produto = Produto::create([
            'nome' => 'Pastel',
            'preco' => 7.00,
            'estoque' => 15,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Cliente Teste',
            'cliente_telefone' => '11999999999',
            'tipo' => 'retirada',
            'valor_total' => 0.00,
        ]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 1,
            'preco_unitario' => 7.00,
        ]);

        $this->assertNotNull($item->pedido);
        $this->assertEquals($pedido->id, $item->pedido->id);
        $this->assertEquals('Cliente Teste', $item->pedido->cliente_nome);
    }

    /** @test */
    public function pertence_a_um_produto()
    {
        $produto = Produto::create([
            'nome' => 'Empada',
            'preco' => 6.50,
            'estoque' => 8,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Outro Cliente',
            'cliente_telefone' => '11988888888',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 6.50,
        ]);

        $this->assertNotNull($item->produto);
        $this->assertEquals($produto->id, $item->produto->id);
        $this->assertEquals('Empada', $item->produto->nome);
        $this->assertEquals(6.50, $item->produto->preco);
    }

    /** @test */
    public function preco_unitario_deve_ser_preservado()
    {
        $produto = Produto::create([
            'nome' => 'Coca-Cola',
            'preco' => 5.00,
            'estoque' => 30,
            'disponivel' => true,
            'categoria' => 'bebidas',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Cliente',
            'cliente_telefone' => '11977777777',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        // Preço do produto muda depois
        $produto->update(['preco' => 6.00]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 5.00, // Preço no momento da venda
        ]);

        // O preço unitário deve permanecer 5.00 mesmo que o produto mude para 6.00
        $this->assertEquals(5.00, $item->preco_unitario);
        $this->assertNotEquals($produto->preco, $item->preco_unitario); // 6.00 != 5.00
    }

    /** @test */
    public function atualiza_estoque_do_produto_ao_criar_item()
    {
        $estoqueInicial = 10;
        $produto = Produto::create([
            'nome' => 'Risole',
            'preco' => 4.50,
            'estoque' => $estoqueInicial,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Cliente',
            'cliente_telefone' => '11966666666',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $quantidadeComprada = 3;
        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => $quantidadeComprada,
            'preco_unitario' => 4.50,
        ]);

        // Atualizar produto para pegar estoque atualizado
        $produto->refresh();

        $estoqueEsperado = $estoqueInicial - $quantidadeComprada; // 10 - 3 = 7
        $this->assertEquals($estoqueEsperado, $produto->estoque);
    }

    /** @test */
    public function valida_quantidade_positiva()
    {
        $produto = Produto::create([
            'nome' => 'Bolinha de Queijo',
            'preco' => 3.50,
            'estoque' => 5,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Cliente',
            'cliente_telefone' => '11955555555',
            'tipo' => 'retirada',
            'valor_total' => 0.00,
        ]);

        // Não deve aceitar quantidade zero ou negativa
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 0, // Quantidade inválida
            'preco_unitario' => 3.50,
        ]);
    }
}
