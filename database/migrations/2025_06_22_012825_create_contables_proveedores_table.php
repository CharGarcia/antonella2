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
        Schema::create('contables_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_proveedor_id')->constrained('datos_proveedores')->onDelete('cascade');
            $table->string('cuenta_por_pagar', 20)->nullable();
            $table->string('cuenta_gasto_predeterminada', 20)->nullable();
            $table->string('cuenta_inventario_predeterminada', 20)->nullable();
            $table->string('cuenta_anticipo', 20)->nullable();
            $table->string('centro_costo', 20)->nullable();
            $table->string('proyecto', 20)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contables_proveedores');
    }
};
