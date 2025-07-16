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
        Schema::create('datos_financieros_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_proveedor_id')->constrained('datos_proveedores')->onDelete('cascade');
            $table->decimal('limite_credito', 12, 2)->nullable();
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
        Schema::dropIfExists('datos_financieros_proveedores');
    }
};
