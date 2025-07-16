<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class KpiProveedor extends Model
{
    protected $table = 'kpi_proveedores';

    protected $fillable = [
        'datos_proveedor_id',
        'total_compras_anual',
        'cantidad_facturas',
        'monto_promedio_compra',
        'ultima_compra_fecha',
        'ultima_compra_monto',
        'dias_promedio_pago',
        'porcentaje_entregas_a_tiempo',
        'porcentaje_entregas_fuera_plazo',
        'porcentaje_devoluciones',
        'porcentaje_reclamos',
        'cantidad_incidentes',
        'saldo_por_pagar',
        'promedio_mensual',
        'productos_frecuentes',
    ];

    protected $casts = [
        'ultima_compra_fecha' => 'date',
        'productos_frecuentes' => 'array',
        'total_compras_anual' => 'decimal:2',
        'monto_promedio_compra' => 'decimal:2',
        'saldo_por_pagar' => 'decimal:2',
        'porcentaje_entregas_a_tiempo' => 'decimal:2',
        'porcentaje_entregas_fuera_plazo' => 'decimal:2',
        'porcentaje_devoluciones' => 'decimal:2',
        'porcentaje_reclamos' => 'decimal:2'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
