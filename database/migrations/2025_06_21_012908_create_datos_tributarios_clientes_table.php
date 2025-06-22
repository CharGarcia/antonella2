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
        Schema::create('datos_tributarios_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('datos_cliente_id')->constrained('datos_clientes')->onDelete('cascade');
            $table->boolean('agente_retencion')->default(false);
            $table->boolean('contribuyente_especial')->default(false);
            $table->boolean('obligado_contabilidad')->default(false);
            $table->string('regimen_tributario')->nullable();
            $table->string('retencion_fuente')->nullable();
            $table->string('retencion_iva')->nullable();
            $table->json('porcentajes_retencion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datos_tributarios_clientes');
    }
};

/* | Campo                    | Propósito                                    |
| ------------------------ | -------------------------------------------- |
| `agente_retencion`       | Si el cliente es agente de retención.        |
| `contribuyente_especial` | Si tiene esta designación tributaria.        |
| `obligado_contabilidad`  | Si debe llevar contabilidad formal.          |
| `regimen_tributario`     | Tipo de régimen: RIMPE, general, etc.        |
| `retencion_fuente`       | Porcentaje o tipo de retención en la fuente. |
| `retencion_iva`          | Porcentaje o tipo de retención de IVA.       |
| `porcentajes_retencion`  | JSON con tasas especiales (si aplica).       |
 */
