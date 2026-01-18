<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoTest extends TestCase
{
    use RefreshDatabase; // Limpa o banco a cada teste

    /** @test */
    public function pode_criar_um_pedido()
    {
        // A Factory agora cuida de criar o User automaticamente!
        $pedido = Pedido::factory()->create();

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedido->id,
            'user_id' => $pedido->user_id
        ]);
    }

    /** @test */
    public function pedido_pode_ter_itens()
    {
        $pedido = Pedido::factory()->create();
        
        // Criamos itens ligados a esse pedido
        $item = PedidoItem::factory()->create([
            'pedido_id' => $pedido->id
        ]);

        $this->assertTrue($pedido->itens->contains($item));
    }

    /** @test */
    public function calcula_valor_total_corretamente()
    {
        // 1. Cria usuário e pedido
        $pedido = Pedido::factory()->create(['total' => 0]);

        // 2. Simula itens (sem subtotal, pois removemos do banco)
        PedidoItem::factory()->create([
            'pedido_id' => $pedido->id,
            'quantidade' => 2,
            'preco_unitario' => 10.00
        ]);

        PedidoItem::factory()->create([
            'pedido_id' => $pedido->id,
            'quantidade' => 1,
            'preco_unitario' => 5.00
        ]);

        // 3. Recalcula (Simulando a lógica do Controller)
        $totalCalculado = $pedido->itens->sum(function($item) {
            return $item->quantidade * $item->preco_unitario;
        });

        // (2 * 10) + (1 * 5) = 25
        $this->assertEquals(25.00, $totalCalculado);
    }
}
