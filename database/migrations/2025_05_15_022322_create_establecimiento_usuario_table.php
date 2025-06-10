<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEstablecimientoUsuarioTable extends Migration
{
    public function up()
    {
        Schema::create('establecimiento_usuario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establecimiento_id')->constrained('establecimientos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'establecimiento_id']); // evita duplicados
        });
    }

    public function down()
    {
        Schema::dropIfExists('establecimiento_usuario');
    }
}
