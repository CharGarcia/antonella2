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
        Schema::create('retenciones_sri', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_retencion')->unique();
            $table->string('concepto');
            $table->string('observaciones');
            $table->decimal('porcentaje', 5, 2); // ejemplo: 10.00%
            $table->string('impuesto'); // Ej: Renta, IVA
            $table->string('codigo_ats');
            $table->string('estado')->default('activo');
            $table->date('vigencia_desde')->nullable();
            $table->date('vigencia_hasta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retenciones_sri');
    }
};
