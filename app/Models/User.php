<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Aqui eu listei o que pode ser preenchido no cadastro. 
     * Tirei o 'is_admin' e coloquei o 'role', porque agora é esse campo 
     * que define se o cara é cliente, patrão ou quem frita o salgado.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Criei essa funçãozinha pra não ter que ficar escrevendo regra toda hora.
     * Se eu perguntar 'é admin?', ela já me responde direto olhando o cargo.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
