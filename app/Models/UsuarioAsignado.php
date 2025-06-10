<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioAsignado extends Model
{
    protected $table = 'usuario_asignado';

    protected $fillable = [
        'id_admin',
        'id_user',
    ];
}
