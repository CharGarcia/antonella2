<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Personas\Persona;

class DatosProveedor extends Model
{
    protected $table = 'datos_proveedores';

    protected $fillable = [
        'persona_id',
        'codigo_interno',
        'categoria_proveedor',
        'segmento',
        'fecha_registro',
        'comprador_asignado',
        'zona',
        'clasificacion',
        'inicio_relacion',
        'estado',
        'configuracion_especial'
    ];

    protected $casts = [
        'configuracion_especial' => 'array',
        'fecha_registro' => 'date',
        'inicio_relacion' => 'date'
    ];

    protected $appends = [
        'inicio_relacion_formatted',
    ];

    public function getInicioRelacionFormattedAttribute(): ?string
    {
        return $this->inicio_relacion
            ? $this->inicio_relacion->format('d/m/Y')
            : null;
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function financieros()
    {
        return $this->hasOne(DatosFinancierosProveedor::class);
    }

    public function tributarios()
    {
        return $this->hasOne(DatosTributariosProveedor::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoProveedor::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialProveedor::class);
    }

    public function contables()
    {
        return $this->hasOne(ContablesProveedor::class);
    }

    public function configuracion()
    {
        return $this->hasOne(ConfiguracionProveedor::class);
    }

    public function kpi()
    {
        return $this->hasOne(KpiProveedor::class);
    }
}


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
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}

class DatosTributariosProveedor extends Model
{

    protected $table = 'datos_tributarios_proveedores';
    protected $fillable = [
        'datos_proveedor_id',
        'agente_retencion',
        'contribuyente_especial',
        'obligado_contabilidad',
        'parte_relacionada',
        'regimen_tributario',
        'codigo_tipo_proveedor_sri',
        'retencion_fuente_codigo',
        'retencion_fuente_porcentaje',
        'retencion_iva_porcentaje',
        'porcentajes_retencion'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}

class DocumentoProveedor extends Model
{
    protected $table = 'documento_proveedores';
    protected $fillable = ['datos_proveedor_id', 'tipo', 'archivo'];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}

class HistorialProveedor extends Model
{

    protected $table = 'historial_proveedores';
    protected $fillable = ['datos_proveedor_id', 'descripcion', 'tipo', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}

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
        return $this->belongsTo(DatosProveedor::class);
    }
}

class ConfiguracionProveedor extends Model
{
    protected $table = 'configuracion_proveedores';

    protected $fillable = [
        'datos_proveedor_id',
        'notas'
    ];


    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}
