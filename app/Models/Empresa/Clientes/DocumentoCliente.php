<?php

namespace App\Models\Empresa\Clientes;

use Illuminate\Database\Eloquent\Model;

class DocumentoCliente extends Model
{
    protected $table = 'documento_clientes';

    protected $fillable = ['datos_cliente_id', 'tipo', 'archivo'];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class);
    }
}
