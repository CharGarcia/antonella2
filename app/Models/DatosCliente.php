<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosCliente extends Model
{
    protected $fillable = [
        'persona_id',
        'codigo_interno',
        'categoria_cliente',
        'segmento',
        'fecha_registro',
        'vendedor_asignado',
        'lista_precios',
        'canal_venta',
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

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function financieros()
    {
        return $this->hasOne(DatosFinancierosCliente::class);
    }

    public function tributarios()
    {
        return $this->hasOne(DatosTributariosCliente::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoCliente::class);
    }

    public function historial()
    {
        return $this->hasMany(HistorialCliente::class);
    }

    public function kpi()
    {
        return $this->hasOne(KpiCliente::class);
    }

    public function configuracion()
    {
        return $this->hasOne(ConfiguracionCliente::class);
    }
}

class DatosFinancierosCliente extends Model
{
    protected $fillable = [
        'datos_cliente_id',
        'cupo_credito',
        'dias_credito',
        'forma_pago',
        'observaciones_crediticias',
        'historial_pagos',
        'nivel_riesgo'
    ];

    protected $casts = [
        'historial_pagos' => 'array',
        'cupo_credito' => 'decimal:2'
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}

class DatosTributariosCliente extends Model
{
    protected $fillable = [
        'datos_cliente_id',
        'agente_retencion',
        'contribuyente_especial',
        'obligado_contabilidad',
        'regimen_tributario',
        'retencion_fuente',
        'retencion_iva',
        'porcentajes_retencion'
    ];

    protected $casts = [
        'agente_retencion' => 'boolean',
        'contribuyente_especial' => 'boolean',
        'obligado_contabilidad' => 'boolean',
        'porcentajes_retencion' => 'array'
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}

class DocumentoCliente extends Model
{
    protected $fillable = ['datos_cliente_id', 'tipo', 'archivo'];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}

class HistorialCliente extends Model
{
    protected $fillable = ['datos_cliente_id', 'descripcion', 'tipo', 'fecha'];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}

class KpiCliente extends Model
{
    protected $fillable = [
        'datos_cliente_id',
        'total_ventas',
        'ultima_compra_fecha',
        'ultima_compra_monto',
        'dias_promedio_pago',
        'saldo_por_cobrar',
        'promedio_mensual',
        'productos_frecuentes'
    ];

    protected $casts = [
        'ultima_compra_fecha' => 'date',
        'productos_frecuentes' => 'array'
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}

class ConfiguracionCliente extends Model
{
    protected $table = 'configuracion_clientes';

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
