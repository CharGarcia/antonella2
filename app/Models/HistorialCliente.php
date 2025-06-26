<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class HistorialCliente extends Model
{
    protected $table = 'historial_clientes';

    protected $fillable = [
        'datos_cliente_id',
        'descripcion',
        'tipo',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class, 'datos_cliente_id');
    }
}
