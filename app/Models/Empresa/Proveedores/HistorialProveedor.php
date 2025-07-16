<?php

namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class HistorialProveedor extends Model
{
    protected $table = 'historial_proveedores';

    protected $fillable = [
        'datos_proveedor_id',
        'descripcion',
        'tipo',
        'fecha'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
