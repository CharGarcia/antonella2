<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmenuEstablecimientoUsuario extends Model
{
    protected $table = 'submenu_establecimiento_usuario'; // 👈 esto es crucial
    protected $fillable = ['user_id', 'establecimiento_id', 'submenu_id', 'ver', 'crear', 'modificar', 'eliminar'];

    public function submenu()
    {
        return $this->belongsTo(Submenu::class, 'submenu_id');
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
