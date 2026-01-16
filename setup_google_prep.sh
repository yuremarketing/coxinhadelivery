#!/bin/bash

echo "🏗️ Preparando o terreno para Login Social (Google)..."

# 1. INSTALAR O PACOTE SOCIALITE (Oficial do Laravel)
echo "📦 Instalando laravel/socialite..."
docker compose exec laravel.test composer require laravel/socialite

# 2. CRIAR MIGRATION PARA ADICIONAR COLUNAS (Sem perder dados)
echo "🗄️ Criando migration para google_id..."
cat << 'EOF' > database/migrations/2025_12_27_999999_add_google_fields_to_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona o campo google_id (pode ser nulo para quem usa senha normal)
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->after('email');
            }
            // Adiciona campo para foto do avatar do Google
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'avatar']);
        });
    }
};
EOF

# 3. RODAR A MIGRATION
echo "🔄 Atualizando o Banco de Dados..."
docker compose exec laravel.test php artisan migrate

# 4. ATUALIZAR O MODEL USER
# Precisamos avisar o Laravel que 'google_id' e 'avatar' podem ser preenchidos
echo "📝 Atualizando app/Models/User.php..."
cat << 'EOF' > app/Models/User.php
<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',      // Já tínhamos adicionado
        'google_id', // NOVO: ID do Google
        'avatar',    // NOVO: Foto do Google
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
EOF

echo "---------------------------------------------------"
echo "✅ Ufa! O terreno está pronto."
echo "O banco de dados agora aceita logins do Google."
