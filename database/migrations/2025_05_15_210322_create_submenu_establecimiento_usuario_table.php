<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmenuEstablecimientoUsuarioTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('submenu_establecimiento_usuario', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('establecimiento_id');
            $table->unsignedBigInteger('submenu_id');

            $table->boolean('ver')->default(false);
            $table->boolean('crear')->default(false);
            $table->boolean('modificar')->default(false);
            $table->boolean('eliminar')->default(false);

            $table->timestamps();

            // Evita duplicar la misma tripleta user-establecimiento-submenu
            // con un nombre de índice corto para cumplir el límite de MySQL
            $table->unique(
                ['user_id', 'establecimiento_id', 'submenu_id'],
                'ux_seu_user_est_sub'
            );

            // Llaves foráneas
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('establecimiento_id')
                ->references('id')->on('establecimientos')
                ->onDelete('cascade');
            $table->foreign('submenu_id')
                ->references('id')->on('submenus')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submenu_establecimiento_usuario');
    }
};
