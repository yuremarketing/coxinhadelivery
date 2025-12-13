<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Produto;
use App\Models\Pedido;
use App\Models\PedidoItem;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class PedidoControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_cria_pedido_com_sucesso()
    {
        $produto = Produto::create([
            'nome' => 'Coxinha de Frango Teste',
            'descricao' => 'Para testes',
            'preco' => 5.50,
            'categoria' => 'coxinhas',
            'disponivel' => true,
            'estoque' => 10
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente API Test',
            'cliente_telefone' => '11999998888',
            'tipo' => 'entrega',
            'observacoes' => 'Teste via API',
            'itens' => [
                [
                    'produto_id' => $produto->id,
                    'quantidade' => 2
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Pedido criado com sucesso'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'pedido_id',
                         'numero_pedido',
                         'valor_total',
                         'status'
                     ]
                 ]);

        $this->assertDatabaseHas('pedidos', [
            'cliente_nome' => 'Cliente API Test',
            'cliente_telefone' => '11999998888',
            'status' => 'pendente'
        ]);

        $this->assertEquals(8, $produto->fresh()->estoque);
    }

    #[Test]
    public function api_nao_cria_pedido_com_produto_indisponivel()
    {
        $produto = Produto::create([
            'nome' => 'Produto Indisponível',
            'descricao' => 'Teste',
            'preco' => 10.00,
            'categoria' => 'teste',
            'disponivel' => false,
            'estoque' => 5
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Teste',
            'cliente_telefone' => '11988887777',
            'tipo' => 'retirada',
            'itens' => [
                [
                    'produto_id' => $produto->id,
                    'quantidade' => 1
                ]
            ]
        ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false
                 ]);
    }

    #[Test]
    public function api_nao_cria_pedido_com_estoque_insuficiente()
    {
        $produto = Produto::create([
            'nome' => 'Produto Estoque Baixo',
            'descricao' => 'Teste',
            'preco' => 7.50,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 2
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Teste',
            'cliente_telefone' => '11977776666',
            'tipo' => 'entrega',
            'itens' => [
                [
                    'produto_id' => $produto->id,
                    'quantidade' => 5
                ]
            ]
        ]);

        $response->assertStatus(500)
                 ->assertJson([
                     'success' => false
                 ]);
    }

    #[Test]
    public function api_busca_pedido_por_numero()
    {
        $produto = Produto::create([
            'nome' => 'Produto para Busca',
            'descricao' => 'Teste',
            'preco' => 6.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 10
        ]);

        $createResponse = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Busca',
            'cliente_telefone' => '11966665555',
            'tipo' => 'retirada',
            'itens' => [
                [
                    'produto_id' => $produto->id,
                    'quantidade' => 1
                ]
            ]
        ]);

        $numeroPedido = $createResponse->json('data.numero_pedido');

        $response = $this->getJson("/api/pedidos/{$numeroPedido}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Pedido encontrado'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'numero_pedido',
                         'cliente_nome',
                         'cliente_telefone',
                         'status',
                         'tipo',
                         'valor_total',
                         'created_at',
                         'itens'
                     ]
                 ]);
    }

    #[Test]
    public function api_retorna_erro_ao_buscar_pedido_inexistente()
    {
        $response = $this->getJson('/api/pedidos/CX999999999999');

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Pedido não encontrado'
                 ]);
    }

    #[Test]
    public function api_lista_pedidos_no_admin()
    {
        // Criar usuário admin
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123')
        ]);

        // Criar token para o usuário
        $token = $user->createToken('test-token')->plainTextToken;

        $produto = Produto::create([
            'nome' => 'Produto Admin',
            'descricao' => 'Teste',
            'preco' => 5.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 20
        ]);

        // Criar pedidos com autenticação
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pedidos', [
                'cliente_nome' => 'Cliente 1',
                'cliente_telefone' => '11911112222',
                'tipo' => 'entrega',
                'itens' => [[
                    'produto_id' => $produto->id,
                    'quantidade' => 1
                ]]
            ]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/pedidos', [
                'cliente_nome' => 'Cliente 2',
                'cliente_telefone' => '11933334444',
                'tipo' => 'retirada',
                'itens' => [[
                    'produto_id' => $produto->id,
                    'quantidade' => 2
                ]]
            ]);

        // Agora lista os pedidos com autenticação
        $response = $this->withHeader('Authorization', "Bearer {$token}")
                        ->getJson('/api/admin/pedidos');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Pedidos listados com sucesso'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'current_page',
                         'data',
                         'total'
                     ]
                 ]);

        $data = $response->json('data.data');
        $this->assertCount(2, $data);
    }

    #[Test]
    public function api_valida_dados_obrigatorios_ao_criar_pedido()
    {
        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Teste',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['cliente_telefone', 'tipo', 'itens']);
    }

    #[Test]
    public function api_atualiza_status_do_pedido()
    {
        // Criar usuário e token
        $user = User::create([
            'name' => 'Admin Status',
            'email' => 'status@test.com',
            'password' => bcrypt('password123')
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $produto = Produto::create([
            'nome' => 'Produto Status',
            'descricao' => 'Teste',
            'preco' => 8.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 15
        ]);

        $createResponse = $this->withHeader('Authorization', "Bearer {$token}")
                              ->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Status',
            'cliente_telefone' => '11955556666',
            'tipo' => 'entrega',
            'itens' => [[
                'produto_id' => $produto->id,
                'quantidade' => 1
            ]]
        ]);

        $pedidoId = $createResponse->json('data.pedido_id');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                        ->putJson("/api/admin/pedidos/{$pedidoId}/status", [
            'status' => 'confirmado'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Status atualizado com sucesso'
                 ]);

        $this->assertDatabaseHas('pedidos', [
            'id' => $pedidoId,
            'status' => 'confirmado'
        ]);
    }

    #[Test]
    public function api_nao_atualiza_para_status_invalido()
    {
        // Criar usuário e token
        $user = User::create([
            'name' => 'Admin Invalid',
            'email' => 'invalid@test.com',
            'password' => bcrypt('password123')
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $produto = Produto::create([
            'nome' => 'Produto Status Inválido',
            'descricao' => 'Teste',
            'preco' => 9.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 5
        ]);

        $createResponse = $this->withHeader('Authorization', "Bearer {$token}")
                              ->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Inválido',
            'cliente_telefone' => '11944445555',
            'tipo' => 'retirada',
            'itens' => [[
                'produto_id' => $produto->id,
                'quantidade' => 1
            ]]
        ]);

        $pedidoId = $createResponse->json('data.pedido_id');

        $response = $this->withHeader('Authorization', "Bearer {$token}")
                        ->putJson("/api/admin/pedidos/{$pedidoId}/status", [
            'status' => 'status_invalido'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Status inválido'
                 ]);
    }

    #[Test]
    public function api_cria_pedido_com_multiplos_itens()
    {
        $produto1 = Produto::create([
            'nome' => 'Produto 1',
            'descricao' => 'Teste 1',
            'preco' => 5.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 10
        ]);

        $produto2 = Produto::create([
            'nome' => 'Produto 2',
            'descricao' => 'Teste 2',
            'preco' => 7.00,
            'categoria' => 'teste',
            'disponivel' => true,
            'estoque' => 8
        ]);

        $response = $this->postJson('/api/pedidos', [
            'cliente_nome' => 'Cliente Múltiplos',
            'cliente_telefone' => '11933332222',
            'tipo' => 'entrega',
            'itens' => [
                [
                    'produto_id' => $produto1->id,
                    'quantidade' => 2
                ],
                [
                    'produto_id' => $produto2->id,
                    'quantidade' => 1
                ]
            ]
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true
                 ]);

        $pedidoId = $response->json('data.pedido_id');
        $itensCount = PedidoItem::where('pedido_id', $pedidoId)->count();
        
        $this->assertEquals(2, $itensCount);
        
        $this->assertEquals(8, $produto1->fresh()->estoque);
        $this->assertEquals(7, $produto2->fresh()->estoque);
    }
}
