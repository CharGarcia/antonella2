<?php

namespace App\Models\Empresa\Clientes;

use Illuminate\Database\Eloquent\Model;

class ContablesCliente extends Model
{
    protected $table = 'contables_clientes';

    protected $fillable = [
        'datos_cliente_id',
        'cta_contable_cliente',
        'cta_anticipos_cliente',
        'cta_ingresos_diferidos',
        'centro_costo',
        'proyecto',
        'segmento_contable',
        'indicador_contab_separada',
    ];

    public function datosCliente()
    {
        return $this->belongsTo(DatosCliente::class, 'datos_cliente_id');
    }
}
