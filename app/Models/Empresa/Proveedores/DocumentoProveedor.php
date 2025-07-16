<?php
namespace App\Models\Empresa\Proveedores;

use Illuminate\Database\Eloquent\Model;

class DocumentoProveedor extends Model
{

    protected $table = 'documento_proveedores';
    protected $fillable = [
        'datos_proveedor_id',
        'tipo',
        'archivo'
    ];

    public function datosProveedor()
    {
        return $this->belongsTo(DatosProveedor::class, 'datos_proveedor_id');
    }
}
