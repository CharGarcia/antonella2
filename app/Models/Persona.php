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
        'id_vendedor',
        'tipo',
        'tipo_empresa',
        'nombre_comercial',
        'id_banco',
        'tipo_cuenta',
        'numero_cuenta',
        'genero',
        'provincia',
        'ciudad',
        'fecha_nacimiento',
        'estado',
    ];

    protected $casts = [
        'tipo' => 'array',
        'parte_relacionada' => 'boolean',
        'estado' => 'boolean',
        'fecha_nacimiento' => 'date',
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

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'id_vendedor');
    }

    public function banco()
    {
        return $this->belongsTo(Banco::class, 'id_banco');
    }

    // Scope para clientes
    public function scopeClientes($query)
    {
        return $query->whereJsonContains('tipo', 'cliente');
    }
}
