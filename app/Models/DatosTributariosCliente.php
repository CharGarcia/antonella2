<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatosTributariosCliente extends Model
{
    protected $table = 'datos_tributarios_clientes';

    protected $fillable = [
        'datos_cliente_id',
        'agente_retencion',
        'contribuyente_especial',
        'obligado_contabilidad',
        'regimen_tributario',
        'retencion_fuente',
        'retencion_iva',
        'porcentajes_retencion',
    ];

    protected $casts = [
        'agente_retencion' => 'boolean',
        'contribuyente_especial' => 'boolean',
        'obligado_contabilidad' => 'boolean',
        'porcentajes_retencion' => 'array',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class, 'datos_cliente_id');
    }
}
