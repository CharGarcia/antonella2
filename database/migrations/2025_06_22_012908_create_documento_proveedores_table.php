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
        Schema::create('documento_proveedores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_proveedor_id')->constrained('datos_proveedores')->onDelete('cascade');
            $table->string('tipo')->nullable(); // contrato, RUC, certificado
            $table->string('archivo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos_proveedores');
    }
};

