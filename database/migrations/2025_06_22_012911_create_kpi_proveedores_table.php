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
        Schema::create('kpi_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_proveedor_id')->constrained('datos_proveedores')->onDelete('cascade');
            $table->decimal('total_compras_anual', 12, 2)->nullable();
            $table->integer('cantidad_facturas')->nullable();
            $table->decimal('monto_promedio_compra', 12, 2)->nullable();
            $table->date('ultima_compra_fecha')->nullable();
            $table->decimal('ultima_compra_monto', 12, 2)->nullable();
            $table->integer('dias_promedio_pago')->nullable();
            $table->decimal('porcentaje_entregas_a_tiempo', 12, 2)->nullable();
            $table->decimal('porcentaje_entregas_fuera_plazo', 12, 2)->nullable();
            $table->decimal('porcentaje_devoluciones', 12, 2)->nullable();
            $table->decimal('porcentaje_reclamos', 12, 2)->nullable();
            $table->integer('cantidad_incidentes')->nullable();
            $table->decimal('saldo_por_pagar', 12, 2)->nullable();
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
        Schema::dropIfExists('kpi_proveedores');
    }
};
