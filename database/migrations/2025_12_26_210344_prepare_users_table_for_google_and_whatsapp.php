<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Aplica as mudanças para o Google e WhatsApp.
     */
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            // 1. Identificação Social (Abaixo do ID)
            if (!Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }

            // 2. Regra de Negócio: Telefone (Abaixo do Avatar)
            if (!Schema::hasColumn('users', 'telefone')) {
                $table->string('telefone')->nullable()->after('avatar');
            }
            if (!Schema::hasColumn('users', 'telefone_verificado_at')) {
                $table->timestamp('telefone_verificado_at')->nullable()->after('telefone');
            }
            
            // 3. Segurança: Tornar senha opcional para quem usa Google
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverte as mudanças se algo der errado.
     */
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['google_id', 'avatar', 'telefone', 'telefone_verificado_at']);
        });
    }
};
