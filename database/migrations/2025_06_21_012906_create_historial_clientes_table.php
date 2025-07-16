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
        Schema::create('historial_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->text('descripcion');
            $table->string('tipo')->nullable(); // contacto, correo, modificación
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_clientes');
    }
};


/* | Campo         | Propósito                                     |
| ------------- | --------------------------------------------- |
| `descripcion` | Descripción de la interacción o cambio.       |
| `tipo`        | Tipo de evento: correo, contacto, edición.    |
| `fecha`       | Fecha y hora del evento (por defecto actual). |
 */
