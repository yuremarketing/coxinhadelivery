<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\Produto;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PedidoItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function pode_criar_um_item_de_pedido()
    {
        // As factories cuidam de criar User -> Pedido -> Produto
        $item = PedidoItem::factory()->create();

        $this->assertDatabaseHas('pedido_itens', [
            'id' => $item->id
        ]);
    }

    /** @test */
    public function pertence_a_um_pedido()
    {
        $item = PedidoItem::factory()->create();
        $this->assertInstanceOf(Pedido::class, $item->pedido); // Mudamos a relação no Model para 'pedido' não 'pedidos' se for belongsTo
    }

    /** @test */
    public function pertence_a_um_produto()
    {
        $item = PedidoItem::factory()->create();
        $this->assertInstanceOf(Produto::class, $item->produto);
    }
}
