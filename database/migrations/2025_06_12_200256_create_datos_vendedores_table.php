<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_vendedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $table->string('codigo_interno')->nullable()->index();
            $table->string('perfil')->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('zona')->nullable();
            $table->date('inicio_relacion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->json('informacion_adicional')->nullable();
            $table->decimal('monto_ventas_asignado', 12, 2)
                ->default(0)
                ->comment('Monto objetivo o asignado de ventas');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('datos_vendedores');
    }
};
