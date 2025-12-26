<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\PedidoItem;
use PHPUnit\Framework\Attributes\Test;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function pode_criar_um_pedido()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'João Silva',
            'cliente_telefone' => '11999999999',
            'cliente_email' => 'joao@email.com',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'observacoes' => 'Sem cebola',
            'valor_total' => 25.50
        ]);

        $this->assertNotNull($pedido);
        $this->assertEquals('João Silva', $pedido->cliente_nome);
        $this->assertEquals('pendente', $pedido->status);
        $this->assertEquals('entrega', $pedido->tipo);
        $this->assertStringStartsWith('CX', $pedido->numero_pedido);
    }

    #[Test]
    public function gera_numero_pedido_automaticamente()
    {
        $pedido1 = Pedido::create([
            'cliente_nome' => 'Cliente 1',
            'cliente_telefone' => '11999999999',
            'status' => 'pendente',
            'tipo' => 'retirada',
            'valor_total' => 15.00
        ]);

        $pedido2 = Pedido::create([
            'cliente_nome' => 'Cliente 2',
            'cliente_telefone' => '11888888888',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'valor_total' => 20.00
        ]);

        $this->assertNotEquals($pedido1->numero_pedido, $pedido2->numero_pedido);
        $this->assertMatchesRegularExpression('/^CX\d{12}$/', $pedido1->numero_pedido);
        $this->assertMatchesRegularExpression('/^CX\d{12}$/', $pedido2->numero_pedido);
    }

    #[Test]
    public function pedido_pode_ter_itens()
    {
        $produto = Produto::create([
            'nome' => 'Coxinha de Frango',
            'descricao' => 'Coxinha tradicional',
            'preco' => 5.50,
            'categoria' => 'coxinhas',
            'disponivel' => true,
            'estoque' => 100
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Maria Santos',
            'cliente_telefone' => '11988888888',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'valor_total' => 11.00
        ]);

        $item = PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto->id,
            'quantidade' => 2,
            'preco_unitario' => 5.50,
            'subtotal' => 11.00
        ]);

        $this->assertCount(1, $pedido->itens);
        $this->assertEquals(2, $pedido->itens->first()->quantidade);
        $this->assertEquals(5.50, $pedido->itens->first()->preco_unitario);
    }

    #[Test]
    public function pode_atualizar_status_do_pedido()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'Carlos Silva',
            'cliente_telefone' => '11977777777',
            'status' => 'pendente',
            'tipo' => 'retirada',
            'valor_total' => 30.00
        ]);

        $resultado = $pedido->atualizarStatus('confirmado');
        
        $this->assertTrue($resultado);
        $this->assertEquals('confirmado', $pedido->fresh()->status);
    }

    #[Test]
    public function nao_atualiza_para_status_invalido()
    {
        $pedido = Pedido::create([
            'cliente_nome' => 'Ana Oliveira',
            'cliente_telefone' => '11966666666',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'valor_total' => 18.50
        ]);

        $resultado = $pedido->atualizarStatus('status_invalido');
        
        $this->assertFalse($resultado);
        $this->assertEquals('pendente', $pedido->fresh()->status);
    }

    #[Test]
    public function verifica_se_pode_ser_cancelado()
    {
        $pedidoPendente = Pedido::create([
            'cliente_nome' => 'Pedido Pendente',
            'cliente_telefone' => '11955555555',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'valor_total' => 22.00
        ]);

        $pedidoEntregue = Pedido::create([
            'cliente_nome' => 'Pedido Entregue',
            'cliente_telefone' => '11944444444',
            'status' => 'entregue',
            'tipo' => 'entrega',
            'valor_total' => 33.00
        ]);

        $this->assertTrue($pedidoPendente->podeSerCancelado());
        $this->assertFalse($pedidoEntregue->podeSerCancelado());
    }

    #[Test]
    public function calcula_total_corretamente()
    {
        $produto1 = Produto::create([
            'nome' => 'Coxinha',
            'descricao' => 'Teste',
            'preco' => 5.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 50
        ]);

        $produto2 = Produto::create([
            'nome' => 'Refrigerante',
            'descricao' => 'Teste',
            'preco' => 4.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 100
        ]);

        $pedido = Pedido::create([
            'cliente_nome' => 'Teste Total',
            'cliente_telefone' => '11933333333',
            'status' => 'pendente',
            'tipo' => 'retirada',
            'valor_total' => 0 // Será calculado
        ]);

        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto1->id,
            'quantidade' => 2,
            'preco_unitario' => 5.00,
            'subtotal' => 10.00
        ]);

        PedidoItem::create([
            'pedido_id' => $pedido->id,
            'produto_id' => $produto2->id,
            'quantidade' => 1,
            'preco_unitario' => 4.00,
            'subtotal' => 4.00
        ]);

        $totalCalculado = $pedido->calcularTotal();
        
        $this->assertEquals(14.00, $totalCalculado);
    }

    #[Test]
    public function scope_pendentes_filtra_apenas_pedidos_pendentes()
    {
        Pedido::create([
            'cliente_nome' => 'Pendente 1',
            'cliente_telefone' => '11911111111',
            'status' => 'pendente',
            'tipo' => 'retirada',
            'valor_total' => 10.00
        ]);

        Pedido::create([
            'cliente_nome' => 'Entregue 1',
            'cliente_telefone' => '11922222222',
            'status' => 'entregue',
            'tipo' => 'entrega',
            'valor_total' => 20.00
        ]);

        Pedido::create([
            'cliente_nome' => 'Pendente 2',
            'cliente_telefone' => '11933333333',
            'status' => 'pendente',
            'tipo' => 'entrega',
            'valor_total' => 15.00
        ]);

        $pedidosPendentes = Pedido::pendentes()->get();
        
        $this->assertCount(2, $pedidosPendentes);
        $this->assertEquals('pendente', $pedidosPendentes[0]->status);
        $this->assertEquals('pendente', $pedidosPendentes[1]->status);
    }

    #[Test]
    public function constantes_de_status_e_tipos_estao_corretas()
    {
        $this->assertEquals([
            'pendente' => 'Pendente',
            'confirmado' => 'Confirmado',
            'em_preparo' => 'Em Preparo',
            'pronto' => 'Pronto',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado'
        ], Pedido::STATUS);

        $this->assertEquals([
            'retirada' => 'Retirada',
            'entrega' => 'Entrega'
        ], Pedido::TIPOS);
    }
}
