<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetencionSri extends Model
{
    protected $table = 'retenciones_sri';

    protected $fillable = [
        'codigo_retencion',
        'concepto',
        'observaciones',
        'porcentaje',
        'impuesto',
        'codigo_ats',
        'status',
        'vigencia_desde',
        'vigencia_hasta',
    ];
}
