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
        Schema::create('datos_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $table->string('codigo_interno')->nullable()->index();
            $table->string('categoria_cliente')->nullable();
            $table->string('segmento')->nullable();
            $table->date('fecha_registro')->nullable();
            $table->string('vendedor_asignado')->nullable();
            $table->foreignId('id_lista_precios')->nullable()->constrained('lista_precios')->nullOnDelete();
            $table->string('canal_venta')->nullable();
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
        Schema::dropIfExists('datos_clientes');
    }
};

/*
| Campo                    | Propósito                                                               |
| ------------------------ | ----------------------------------------------------------------------- |
| `persona_id`             | Relaciona al cliente con su información general de la tabla `personas`. |
| `codigo_interno`         | Código único usado internamente para identificar al cliente.            |
| `categoria_cliente`      | Clasificación general: VIP, recurrente, etc.                            |
| `segmento`               | Segmento comercial: corporativo, individual, etc.                       |
| `fecha_registro`         | Fecha en que fue registrado el cliente.                                 |
| `vendedor_asignado`      | Nombre o ID del vendedor encargado de la cuenta.                        |
| `lista_precios`          | Identificador de la lista de precios aplicada al cliente.               |
| `canal_venta`            | Canal a través del cual se vende: online, distribución, etc.            |
| `zona`                   | Zona geográfica o comercial del cliente.                                |
| `clasificacion`          | Mayorista, minorista, institucional, etc.                               |
| `inicio_relacion`        | Fecha en que inició la relación comercial.                              |
| `estado`                 | Estado actual del cliente: activo, inactivo, bloqueado por morosidad.   |
| `configuracion_especial` | JSON con reglas personalizadas: límites, flags, etc.                    |
 */
