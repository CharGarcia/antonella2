<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendedoresTable extends Migration
{
    public function up()
    {
        Schema::create('vendedores', function (Blueprint $table) {
            $table->id();

            // Aseguramos que id_establecimiento sea compatible con la clave foránea
            $table->foreignId('id_establecimiento')
                ->constrained('establecimientos')
                ->onDelete('cascade');

            $table->foreignId('id_user')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->string('numero_identificacion')->nullable();
            $table->string('nombre');
            $table->string('telefono', 10)->nullable();
            $table->text('email')->nullable(); // múltiples correos separados por coma
            $table->text('direccion')->nullable();
            $table->boolean('estado')->default(true); // activo/inactivo
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendedores');
    }
}
