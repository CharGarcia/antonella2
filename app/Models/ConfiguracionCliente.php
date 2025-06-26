<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionCliente extends Model
{
    protected $fillable = [
        'datos_cliente_id',
        'notas',
        'permitir_venta_con_deuda',
        'aplica_descuento',
    ];

    protected $casts = [
        'permitir_venta_con_deuda' => 'boolean',
        'aplica_descuento' => 'boolean',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}
