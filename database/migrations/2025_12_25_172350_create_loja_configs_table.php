<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Aqui é o painel de controle da nave.
     * Em vez de ter que mexer no código pra fechar a loja ou mudar o link da live,
     * a gente guarda tudo aqui. É só o patrão mudar no banco (ou painel) e o App obedece.
     */
    public function up(): void
    {
        Schema::create('loja_configs', function (Blueprint $table) {
            $table->id();
            
            // A TV Coxinha
            $table->string('link_youtube')->nullable()->comment('O link da live da fritadeira');
            
            // Botão de Pânico: Acabou a massa? Fecha a loja no App agora.
            $table->boolean('loja_aberta')->default(true)->comment('Se false, o app mostra FECHADO');
            
            // Gestão de Expectativa: Se a cozinha travar, a gente aumenta esse número.
            $table->integer('tempo_espera_minutos')->default(40)->comment('Tempo médio pro cliente não ficar perguntando');
            
            $table->timestamps();
        });

        // Já cria a configuração inicial pra gente não ter trabalho depois
        DB::table('loja_configs')->insert([
            'link_youtube' => null, // Começa sem live
            'loja_aberta' => true,  // Começa aberta
            'tempo_espera_minutos' => 40,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('loja_configs');
    }
};
