<?php

namespace App\Models\Empresa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establecimiento extends Model
{
    use HasFactory;

    protected $table = 'establecimientos';

    protected $fillable = [
        'empresa_id',
        'serie',
        'nombre_comercial',
        'establecimiento',
        'punto_emision',
        'direccion',
        'logo',
        'factura',
        'nota_credito',
        'nota_debito',
        'guia_remision',
        'retencion',
        'liquidacion_compra',
        'proforma',
        'recibo',
        'ingreso',
        'egreso',
        'orden_compra',
        'pedido',
        'consignacion_venta',
        'decimal_cantidad',
        'decimal_precio',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'decimal_cantidad' => 'integer',
        'decimal_precio' => 'integer',
        'factura' => 'integer',
        'nota_credito' => 'integer',
        'nota_debito' => 'integer',
        'guia_remision' => 'integer',
        'retencion' => 'integer',
        'liquidacion_compra' => 'integer',
        'proforma' => 'integer',
        'recibo' => 'integer',
        'ingreso' => 'integer',
        'egreso' => 'integer',
        'orden_compra' => 'integer',
        'pedido' => 'integer',
        'consignacion_venta' => 'integer',
    ];

    // Relación con Empresa
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
