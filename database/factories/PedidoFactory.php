<?php

namespace Database\Factories;

use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    public function definition(): array
    {
        return [
            'numero_pedido' => 'PED-' . strtoupper($this->faker->unique()->bothify('??###')),
            'cliente_nome' => $this->faker->name(),
            'cliente_telefone' => $this->faker->phoneNumber(),
            'valor_total' => 0,
            'status' => 'pendente',
            'tipo' => 'entrega',
        ];
    }
}
