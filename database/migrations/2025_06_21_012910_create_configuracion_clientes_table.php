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
        Schema::create('configuracion_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->text('notas')->nullable();
            $table->boolean('permitir_venta_con_deuda')->nullable();
            $table->boolean('aplica_descuento')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracion_clientes');
    }
};


/* | Campo                      | Propósito                                         |
| -------------------------- | ------------------------------------------------- |
| `notas`                    | Notas internas del equipo sobre el cliente.       |
| `permitir_venta_con_deuda` | Si se puede vender aún con deuda pendiente.       |
| `aplica_descuento`         | Si se le aplican descuentos personalizados.       |
 */
