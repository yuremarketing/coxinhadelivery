<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Funcionario; // Importando o modelo para criar o motoboy
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Onde a mágica acontece.
     * Comentário: Chamo os seeders específicos aqui para manter o código modular.
     */
    public function run(): void
    {
        // 1. Criar o usuário admin (Pra você conseguir logar depois)
        User::factory()->create([
            'name' => 'Yuri Admin',
            'email' => 'admin@coxinha.com',
            'password' => bcrypt('admin123'),
        ]);

        // 2. Criar um Motoboy inicial (Indispensável para o fluxo de entrega)
        Funcionario::create([
            'nome' => 'Marcio Motoca',
            'cargo' => 'ENTREGADOR',
            'telefone' => '11999999999',
            'placa_veiculo' => 'COX-2025'
        ]);

        // 3. Chamar o seu Seeder de Produtos que já existe no projeto
        $this->call([
            ProdutosSeeder::class,
        ]);
    }
}
