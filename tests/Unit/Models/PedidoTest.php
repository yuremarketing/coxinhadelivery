<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_criar_um_pedido()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'João Silva',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'status' => 'pendente',
            'valor_total' => 0.00,
        ]);

        $this->assertNotNull($pedido);
        $this->assertEquals('João Silva', $pedido->cliente_nome);
        $this->assertEquals('11999999999', $pedido->cliente_telefone);
        $this->assertEquals('entrega', $pedido->tipo);
        $this->assertEquals('pendente', $pedido->status);
        $this->assertEquals(0.00, $pedido->valor_total);
        $this->assertStringStartsWith('CX', $pedido->numero_pedido);
    }

    /** @test */
    public function gera_numero_pedido_unico_automaticamente()
    {
        $pedido1 = Pedido::create([
            'cliente_nome' => 'Cliente 1',
            'cliente_telefone' => '11999999991',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $pedido2 = Pedido::create([
            'cliente_nome' => 'Cliente 2',
            'cliente_telefone' => '11999999992',
            'tipo' => 'retirada',
            'valor_total' => 0.00,
        ]);

        $this->assertNotEquals($pedido1->numero_pedido, $pedido2->numero_pedido);
        $this->assertMatchesRegularExpression('/^CX\d{12}$/', $pedido1->numero_pedido);
        $this->assertMatchesRegularExpression('/^CX\d{12}$/', $pedido2->numero_pedido);
    }

    /** @test */
    public function numero_pedido_tem_formato_correto_CX_ano_mes_dia_sequencia()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'Teste',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        // Formato esperado: CX + ano(4) + mes(2) + dia(2) + sequencia(4)
        // Exemplo: CX202512120001
        $this->assertEquals(14, strlen($pedido->numero_pedido)); // CX + 12 dígitos
        $this->assertEquals(date('Ymd'), substr($pedido->numero_pedido, 2, 8));
    }

    /** @test */
    public function pedido_pode_ter_itens()
    {
        $produto = Produto::create([
            'nome' => 'Coxinha',
            'preco' => 5.50,
            'estoque' => 10,
            'disponivel' => true,
            'categoria' => 'salgados',
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
            'quantidade' => 2,
            'preco_unitario' => $produto->preco,
        ]);

        $this->assertCount(1, $pedido->itens);
        $this->assertEquals('Coxinha', $pedido->itens->first()->produto->nome);
    }

    /** @test */
    public function calcula_valor_total_corretamente()
    {
        $produto1 = Produto::create([
            'nome' => 'Coxinha de Frango',
            'preco' => 5.50,
            'estoque' => 10,
            'disponivel' => true,
            'categoria' => 'salgados',
        ]);

        $produto2 = Produto::create([
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

        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto1->id,
            'quantidade' => 2,
            'preco_unitario' => $produto1->preco,
            'subtotal' => 11.00, // 2 * 5.50
        ]);

        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto2->id,
            'quantidade' => 1,
            'preco_unitario' => $produto2->preco,
            'subtotal' => 4.00, // 1 * 4.00
        ]);

        // Atualiza valor total
        $valorTotal = $pedido->calcularTotal();
        $this->assertEquals(15.00, $valorTotal); // 11.00 + 4.00 = 15.00
    }

    /** @test */
    public function status_pode_ser_atualizado()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'Teste',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'status' => 'pendente',
            'valor_total' => 0.00,
        ]);

        $pedido->update(['status' => 'confirmado']);
        $this->assertEquals('confirmado', $pedido->status);

        $pedido->update(['status' => 'em_preparo']);
        $this->assertEquals('em_preparo', $pedido->status);
    }

    /** @test */
    public function possui_status_validos()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'Teste',
            'cliente_telefone' => '11999999999',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $statusValidos = ['pendente', 'confirmado', 'em_preparo', 'pronto', 'entregue', 'cancelado'];
        
        foreach ($statusValidos as $status) {
            $pedido->status = $status;
            $this->assertEquals($status, $pedido->status);
        }
    }

    /** @test */
    public function tipo_pode_ser_entrega_ou_retirada()
    {
        $pedidoEntrega = Pedido::create([
            'cliente_nome' => 'Cliente Entrega',
            'cliente_telefone' => '11999999991',
            'tipo' => 'entrega',
            'valor_total' => 0.00,
        ]);

        $pedidoRetirada = Pedido::create([
            'cliente_nome' => 'Cliente Retirada',
            'cliente_telefone' => '11999999992',
            'tipo' => 'retirada',
            'valor_total' => 0.00,
        ]);

        $this->assertEquals('entrega', $pedidoEntrega->tipo);
        $this->assertEquals('retirada', $pedidoRetirada->tipo);
    }
}
