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
        Schema::create('establecimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('empresa_id'); // relación con empresas
            $table->string('nombre_comercial');
            $table->string('serie');
            $table->string('direccion');
            $table->string('logo')->nullable();
            $table->unsignedBigInteger('factura')->default(1);
            $table->unsignedBigInteger('nota_credito')->default(1);
            $table->unsignedBigInteger('nota_debito')->default(1);
            $table->unsignedBigInteger('guia_remision')->default(1);
            $table->unsignedBigInteger('retencion')->default(1);
            $table->unsignedBigInteger('liquidacion_compra')->default(1);
            $table->unsignedBigInteger('proforma')->default(1);
            $table->unsignedBigInteger('recibo')->default(1);
            $table->unsignedBigInteger('ingreso')->default(1);
            $table->unsignedBigInteger('egreso')->default(1);
            $table->unsignedBigInteger('orden_compra')->default(1);
            $table->unsignedBigInteger('pedido')->default(1);
            $table->unsignedBigInteger('consignacion_venta')->default(1);
            $table->unsignedTinyInteger('decimal_cantidad')->default(2);
            $table->unsignedTinyInteger('decimal_precio')->default(2);
            $table->string('estado')->default('activo');
            $table->timestamps();
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimientos');
    }
};
