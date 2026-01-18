<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;
use App\Models\Produto;

class PedidoItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Se não tiver pedido, cria um (que já vai criar um usuário por causa do passo 1)
            'pedido_id' => Pedido::factory(),
            
            // Se não tiver produto, cria um
            'produto_id' => Produto::factory(),
            
            'quantidade' => $this->faker->numberBetween(1, 5),
            'preco_unitario' => $this->faker->randomFloat(2, 5, 20),
        ];
    }
}
