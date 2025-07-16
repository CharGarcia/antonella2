<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionProveedor extends Model
{

    protected $table = 'configuracion_proveedores';
    protected $fillable = [
        'datos_proveedor_id',
        'notas'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
