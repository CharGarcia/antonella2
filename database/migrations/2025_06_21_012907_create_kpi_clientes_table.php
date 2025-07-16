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
        Schema::create('kpi_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->decimal('total_ventas', 12, 2)->nullable();
            $table->date('ultima_compra_fecha')->nullable();
            $table->decimal('ultima_compra_monto', 12, 2)->nullable();
            $table->integer('dias_promedio_pago')->nullable();
            $table->decimal('saldo_por_cobrar', 12, 2)->nullable();
            $table->decimal('promedio_mensual', 12, 2)->nullable();
            $table->json('productos_frecuentes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_clientes');
    }
};


/* | Campo                  | Propósito                          |
| ---------------------- | ---------------------------------- |
| `total_ventas`         | Total acumulado en ventas.         |
| `ultima_compra_fecha`  | Fecha de la última compra.         |
| `ultima_compra_monto`  | Monto de la última compra.         |
| `dias_promedio_pago`   | Días promedio que tarda en pagar.  |
| `saldo_por_cobrar`     | Total pendiente de cobro.          |
| `promedio_mensual`     | Promedio de compra mensual.        |
| `productos_frecuentes` | JSON con productos más consumidos. |
 */
