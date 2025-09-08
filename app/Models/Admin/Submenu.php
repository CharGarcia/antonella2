<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Submenu extends Model
{
    protected $fillable = [
        'menu_id',
        'nombre',
        'ruta',
        'icono',
        'orden',
        'estado'
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
