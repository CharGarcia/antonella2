<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('usuario_asignado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_admin')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('id_user')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();

            // Un usuario sólo puede asignarse una vez al mismo admin
            $table->unique(['id_admin', 'id_user']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_asignado');
    }
};
