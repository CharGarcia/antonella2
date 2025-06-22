<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'id_establecimiento',
        'id_user',
        'nombre',
        'tipo_identificacion',
        'numero_identificacion',
        'telefono',
        'email',
        'direccion',
        'tipo',
        'tipo_empresa',
        'nombre_comercial',
        'provincia',
        'ciudad',
        'pais',
        'estado_tipo',
    ];

    protected $casts = [
        'tipo' => 'array',
        'estado_tipo' => 'array',
    ];

    // Relaciones
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Scope para clientes
    public function scopeClientes($query)
    {
        return $query->whereJsonContains('tipo', 'cliente');
    }
}
