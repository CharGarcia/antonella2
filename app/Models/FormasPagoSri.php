<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormasPagoSri extends Model
{
    protected $table = 'formas_pago_sri';
    protected $fillable = ['codigo', 'descripcion', 'estado'];
}
