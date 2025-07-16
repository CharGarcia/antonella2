<?php

namespace App\Models\Empresa\Vendedores;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Personas\Persona;

class DatosVendedor extends Model
{
    protected $table = 'datos_vendedores';

    protected $fillable = [
        'persona_id',
        'codigo_interno',
        'perfil',
        'fecha_registro',
        'zona',
        'inicio_relacion',
        'estado',
        'informacion_adicional',
        'monto_ventas_asignado',
    ];

    protected $casts = [
        'informacion_adicional' => 'array',
        'fecha_registro'        => 'date',
        'inicio_relacion'       => 'date',
    ];

    /**
     * Relación con la persona asociada.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Mutator para asegurarnos de manejar correctamente el estado.
     */
    public function setEstadoAttribute(string $value): void
    {
        $this->attributes['estado'] = strtolower($value);
    }
}
