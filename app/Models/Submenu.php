<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    protected $fillable = [
        'menu_id',
        'nombre',
        'ruta',
        'icono',
        'orden',
        'activo'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
