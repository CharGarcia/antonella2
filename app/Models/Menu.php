<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['nombre', 'icono', 'orden', 'activo'];


    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }
}
