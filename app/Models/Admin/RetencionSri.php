<?php

namespace App\Models\Admin;

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
        'estado',
        'vigencia_desde',
        'vigencia_hasta',
    ];
}
