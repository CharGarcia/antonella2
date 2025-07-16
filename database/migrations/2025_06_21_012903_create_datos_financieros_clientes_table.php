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
        Schema::create('datos_financieros_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->decimal('cupo_credito', 12, 2)->nullable();
            $table->integer('dias_credito')->nullable();
            $table->string('forma_pago')->nullable();
            $table->text('observaciones_crediticias')->nullable();
            $table->json('historial_pagos')->nullable();
            $table->string('nivel_riesgo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_financieros_clientes');
    }
};

/* | Campo                       | Propósito                                           |
| --------------------------- | --------------------------------------------------- |
| `cupo_credito`              | Límite máximo de crédito autorizado.                |
| `dias_credito`              | Número de días otorgados para pago a crédito.       |
| `forma_pago`                | Método preferido: transferencia, efectivo, etc.     |
| `banco`                     | Nombre del banco principal del cliente.             |
| `numero_cuenta`             | Número de cuenta bancaria.                          |
| `observaciones_crediticias` | Notas de riesgo, alertas, historial negativo, etc.  |
| `historial_pagos`           | JSON con pagos realizados (fechas, montos, estado). |
| `nivel_riesgo`              | Clasificación interna del riesgo financiero.        |
 */
