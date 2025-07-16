<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class DatosFinancierosProveedor extends Model
{
    protected $table = 'datos_financieros_proveedores';

    protected $fillable = [
        'datos_proveedor_id',
        'limite_credito',
        'dias_credito',
        'forma_pago',
        'observaciones_crediticias',
        'historial_pagos',
        'nivel_riesgo',
    ];

    protected $casts = [
        'historial_pagos' => 'array',
        'limite_credito' => 'decimal:2'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
