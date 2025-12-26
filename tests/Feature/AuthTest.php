<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usuario_pode_se_registrar()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Novo Usuário',
            'email' => 'novo@email.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Usuário registrado com sucesso'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user',
                         'access_token',
                         'token_type'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'novo@email.com',
            'name' => 'Novo Usuário'
        ]);
    }

    #[Test]
    public function registro_falha_com_senhas_diferentes()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Teste',
            'email' => 'teste@email.com',
            'password' => 'password123',
            'password_confirmation' => 'diferente'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Erro de validação'
                 ])
                 ->assertJsonValidationErrors(['password']);
    }

    #[Test]
    public function registro_falha_com_email_duplicado()
    {
        User::create([
            'name' => 'Existente',
            'email' => 'existente@email.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Novo',
            'email' => 'existente@email.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Erro de validação'
                 ])
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function usuario_pode_fazer_login()
    {
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'login@teste.com',
            'password' => bcrypt('password123')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login@teste.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Login realizado com sucesso'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user',
                         'access_token',
                         'token_type'
                     ]
                 ]);
    }

    #[Test]
    public function login_falha_com_credenciais_invalidas()
    {
        User::create([
            'name' => 'Usuário',
            'email' => 'user@email.com',
            'password' => bcrypt('senhacorreta')
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'user@email.com',
            'password' => 'senhaerrada'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Credenciais inválidas'
                 ]);
    }

    #[Test]
    public function usuario_pode_fazer_logout()
    {
        $user = User::create([
            'name' => 'Logout Test',
            'email' => 'logout@test.com',
            'password' => bcrypt('password123')
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Logout realizado com sucesso'
                 ]);
    }

    #[Test]
    public function usuario_pode_ver_seu_perfil()
    {
        $user = User::create([
            'name' => 'Perfil Test',
            'email' => 'perfil@test.com',
            'password' => bcrypt('password123')
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Dados do usuário'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'user' => [
                             'id',
                             'name',
                             'email'
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function acesso_negado_sem_token_para_perfil()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    #[Test]
    public function usuario_pode_atualizar_seu_perfil()
    {
        $user = User::create([
            'name' => 'Antigo Nome',
            'email' => 'antigo@email.com',
            'password' => bcrypt('password123')
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->putJson('/api/auth/me', [
            'name' => 'Novo Nome',
            'email' => 'novo@email.com'
        ], [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Perfil atualizado com sucesso'
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Novo Nome',
            'email' => 'novo@email.com'
        ]);
    }

    #[Test]
    public function middleware_isadmin_permite_acesso_autenticado()
    {
        $user = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password123')
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        // Testa uma rota admin (deve passar pois o middleware IsAdmin apenas verifica autenticação por enquanto)
        $response = $this->getJson('/api/admin/pedidos', [
            'Authorization' => 'Bearer ' . $token
        ]);

        $response->assertStatus(200);
    }
}
