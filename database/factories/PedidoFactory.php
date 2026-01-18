<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class PedidoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total' => $this->faker->randomFloat(2, 50, 200),
            'status' => 'pendente',
            'numero_pedido' => 'CX' . $this->faker->unique()->numerify('##########'),
        ];
    }
}
