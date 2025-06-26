<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiCliente extends Model
{
    protected $table = 'kpi_clientes';

    protected $fillable = [
        'datos_cliente_id',
        'total_ventas',
        'ultima_compra_fecha',
        'ultima_compra_monto',
        'dias_promedio_pago',
        'saldo_por_cobrar',
        'promedio_mensual',
        'productos_frecuentes',
    ];

    protected $casts = [
        'ultima_compra_fecha' => 'date',
        'productos_frecuentes' => 'array',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class, 'datos_cliente_id');
    }
}
