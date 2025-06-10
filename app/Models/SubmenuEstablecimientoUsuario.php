<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmenuEstablecimientoUsuario extends Model
{
    protected $fillable = ['user_id', 'establecimiento_id', 'submenu_id', 'ver', 'crear', 'modificar', 'eliminar'];

    public function submenu()
    {
        return $this->belongsTo(Submenu::class);
    }

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
