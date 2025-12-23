<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Produto>
 */
class ProdutoFactory extends Factory
{
    protected $model = \App\Models\Produto::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'nome' => $this->faker->word(),            // nome do produto
            'descricao' => $this->faker->sentence(),   // descrição curta
            'preco' => $this->faker->randomFloat(2, 5, 100), // preço entre 5 e 100
            'quantidade' => $this->faker->numberBetween(1, 50), // estoque
        ];
    }
}
