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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->unique();
            $table->string('razon_social');
            $table->string('tipo_contribuyente')->nullable(); // Ej: Natural, Jurídica
            $table->string('regimen')->nullable(); // Ej: RIMPE, Régimen General
            $table->enum('contabilidad', ['SI', 'NO'])->default('NO');
            $table->enum('contribuyente_especial', ['SI', 'NO'])->default('NO');
            $table->enum('agente_retencion', ['SI', 'NO'])->default('NO');
            $table->string('nombre_rep_leg')->nullable();
            $table->string('cedula_rep_leg')->nullable();
            $table->string('nombre_contador')->nullable();
            $table->string('ruc_contador')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('archivo_firma')->nullable();
            $table->string('password_firma')->nullable();
            $table->boolean('estado')->default(true); // activa o inactiva
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
