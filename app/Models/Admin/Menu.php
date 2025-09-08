<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = ['nombre', 'icono', 'orden', 'estado'];


    public function submenus()
    {
        return $this->hasMany(Submenu::class);
    }
}
