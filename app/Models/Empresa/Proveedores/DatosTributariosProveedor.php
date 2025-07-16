<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

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
        'retencion_fuente',
        'retencion_iva',
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
