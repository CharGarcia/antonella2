<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documento_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->string('tipo')->nullable(); // contrato, RUC, certificado
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_clientes');
    }
};

/* | Campo     | Propósito                              |
| --------- | -------------------------------------- |
| `tipo`    | Tipo de documento: contrato, RUC, etc. |
| `archivo` | Ruta o nombre del archivo almacenado.  |
 */
