<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifaIva extends Model
{
    protected $table = 'tarifa_iva';
    protected $fillable = ['codigo', 'descripcion', 'porcentaje', 'estado'];
}
