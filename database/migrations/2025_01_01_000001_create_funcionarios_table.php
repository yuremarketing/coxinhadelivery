<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('telefone')->nullable();
            $table->enum('cargo', ['COZINHEIRO', 'ENTREGADOR', 'FAXINEIRO', 'BALCONISTA', 'OUTRO']);
            $table->string('placa_veiculo')->nullable();
            $table->string('modelo_veiculo')->nullable();
            $table->string('foto_url')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('funcionarios'); }
};
