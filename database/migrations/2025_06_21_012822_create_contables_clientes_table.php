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
        Schema::create('contables_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->string('cta_contable_cliente', 20)->nullable();
            $table->string('cta_anticipos_cliente', 20)->nullable();
            $table->string('cta_ingresos_diferidos', 20)->nullable();
            $table->string('centro_costo', 20)->nullable();
            $table->string('proyecto', 20)->nullable();
            $table->string('segmento_contable', 20)->nullable();
            $table->char('indicador_contab_separada', 1)->default('N');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contables_clientes');
    }
};


