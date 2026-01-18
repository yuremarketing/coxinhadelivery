<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProdutoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => $this->faker->word . ' Deliciosa',
            'descricao' => $this->faker->sentence,
            'preco' => $this->faker->randomFloat(2, 5, 50), // Preço entre 5 e 50 reais
            'imagem' => 'https://via.placeholder.com/150',
        ];
    }
}
