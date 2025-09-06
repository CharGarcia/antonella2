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
        Schema::create('datos_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $table->string('codigo_interno')->nullable()->index();
            $table->string('categoria_proveedor')->nullable();
            $table->string('segmento')->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('comprador_asignado')->nullable();
            $table->string('zona')->nullable();
            $table->string('clasificacion')->nullable();
            $table->date('inicio_relacion')->nullable();
            $table->string('estado')->default('activo');
            $table->json('configuracion_especial')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_proveedores');
    }
};

