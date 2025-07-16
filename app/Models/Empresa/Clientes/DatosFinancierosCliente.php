<?php

namespace App\Models\Empresa\Clientes;

use Illuminate\Database\Eloquent\Model;

class DatosFinancierosCliente extends Model
{
    protected $table = 'datos_financieros_clientes';

    protected $fillable = [
        'datos_cliente_id',
        'cupo_credito',
        'dias_credito',
        'forma_pago',
        'observaciones_crediticias',
        'historial_pagos',
        'nivel_riesgo',
    ];

    protected $casts = [
        'historial_pagos' => 'array',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class, 'datos_cliente_id');
    }
}
