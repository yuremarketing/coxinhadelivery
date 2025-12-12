<?php

// Importa a classe base Migration do Laravel
// Todas as migrations herdam desta classe
use Illuminate\Database\Migrations\Migration;

// Importa Blueprint para definir a estrutura da tabela
// Blueprint = "planta baixa" da tabela
use Illuminate\Database\Schema\Blueprint;

// Importa Schema para interagir com o banco de dados
// Schema = "esquema" do banco (criar, modificar tabelas)
use Illuminate\Support\Facades\Schema;

// Define uma nova classe anônima que extende Migration
// Classe anônima = classe sem nome, só para esta migration
return new class extends Migration
{
    /**
     * Método UP: Executa quando roda "php artisan migrate"
     * Cria ou modifica tabelas no banco
     */
    public function up(): void
    {
        // Schema::create = Cria uma nova tabela no banco
        // 'produtos' = nome da tabela
        // function (Blueprint $table) = função que define as colunas
        Schema::create('produtos', function (Blueprint $table) {
            
            // $table->id() = Cria coluna 'id' como primary key auto increment
            // Equivalente a: id INT PRIMARY KEY AUTO_INCREMENT
            $table->id();
            
            // $table->string('nome') = Cria coluna VARCHAR(255)
            // string = texto curto (até 255 caracteres)
            $table->string('nome');
            
            // text() = campo de texto longo (mais de 255 chars)
            // nullable() = campo pode ficar vazio (NULL no banco)
            $table->text('descricao')->nullable();
            
            // decimal('preco', 8, 2) = número decimal com precisão
            // 8 = total de dígitos (incluindo decimais)
            // 2 = casas decimais
            // Exemplo: 999999.99 (máximo que aceita)
            $table->decimal('preco', 8, 2);
            
            // Categoria do produto: 'salgados', 'bebidas', etc
            // Vai ser um VARCHAR(255) no banco
            $table->string('categoria');
            
            // Campo para armazenar URL ou caminho da imagem
            // nullable() = produto pode não ter imagem
            $table->string('imagem')->nullable();
            
            // boolean() = campo verdadeiro/falso (TINYINT(1) no MySQL)
            // default(true) = valor padrão é TRUE (1)
            // Se disponivel = 1, produto aparece no catálogo
            $table->boolean('disponivel')->default(true);
            
            // integer() = número inteiro (INT no MySQL)
            // default(0) = valor inicial é zero
            // Controla quantas unidades temos em estoque
            $table->integer('estoque')->default(0);
            
            // timestamps() = Cria duas colunas automáticas:
            // created_at: data/hora quando registro foi criado
            // updated_at: data/hora quando registro foi atualizado
            // São atualizados automaticamente pelo Laravel
            $table->timestamps();
        });
    }

    /**
     * Método DOWN: Executa quando roda "php artisan migrate:rollback"
     * Desfaz as alterações do método up()
     */
    public function down(): void
    {
        // Schema::dropIfExists = Apaga a tabela se ela existir
        // Isso garante que podemos reverter a migration
        Schema::dropIfExists('produtos');
    }
};