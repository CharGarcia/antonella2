<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonasTable extends Migration
{
    public function up()
    {
        Schema::create('personas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_establecimiento')->constrained('establecimientos')->onDelete('cascade');
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nombre');
            $table->string('tipo_identificacion')->nullable(); // Ej: RUC, Cédula, Pasaporte
            $table->string('numero_identificacion')->nullable();
            $table->string('telefono', 10)->nullable();
            $table->text('email')->nullable(); // múltiples correos separados por coma
            $table->text('direccion')->nullable();
            $table->json('tipo'); // puede ser ["cliente", "proveedor", "empleado"]
            $table->string('tipo_empresa')->nullable(); // natural, juridica
            $table->string('nombre_comercial')->nullable();
            $table->string('tipo_cuenta', 50)->nullable(); // ahorros, corriente
            $table->string('numero_cuenta')->nullable();
            $table->string('provincia', 50)->nullable();
            $table->string('ciudad', 50)->nullable();
            $table->string('pais', 50)->nullable(); // masculino, femenino, otro
            $table->json('estado_tipo')->nullable(); // Ej: {"cliente":"activo", "empleado":"inactivo"}
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('personas');
    }
}
