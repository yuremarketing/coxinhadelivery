<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\PedidoItem;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class PedidoItemTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
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

    #[Test]
    public function calcula_subtotal_automaticamente_se_nao_fornecido()
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
            'quantidade' => 3,
            'preco_unitario' => 5.50,
        ]);

        $this->assertEquals(16.50, $item->subtotal);
    }

    #[Test]
    public function pertence_a_um_pedido()
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

        $this->assertInstanceOf(Pedido::class, $item->pedido);
        $this->assertEquals($pedido->id, $item->pedido->id);
    }

    #[Test]
    public function pertence_a_um_produto()
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

        $this->assertInstanceOf(Produto::class, $item->produto);
        $this->assertEquals($produto->id, $item->produto->id);
    }

    #[Test]
    public function preco_unitario_deve_ser_preservado()
    {
        $produto = Produto::create([
            'nome' => 'Coxinha de Frango',
            'descricao' => 'Coxinha tradicional',
            'preco' => 10.00,
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

        $produto->update(['preco' => 15.00]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 10.00,
        ]);

        $this->assertEquals(10.00, $item->preco_unitario);
        $this->assertEquals(20.00, $item->subtotal);
    }

    #[Test]
    public function atualiza_estoque_do_produto_ao_criar_item()
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

        $initialStock = $produto->estoque;

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 3,
            'preco_unitario' => 5.50,
        ]);

        $produto->refresh();

        $this->assertEquals($initialStock - 3, $produto->estoque);
    }
}
