<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class ContablesProveedor extends Model
{

    protected $table = 'contables_proveedores';
    protected $fillable = [
        'datos_proveedor_id',
        'cuenta_por_pagar',
        'cuenta_gasto_predeterminada',
        'cuenta_inventario_predeterminada',
        'cuenta_anticipo',
        'centro_costo',
        'proyecto'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class);
    }
}
