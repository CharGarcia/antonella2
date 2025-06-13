<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    use HasFactory;

    protected $table = 'vendedores';

    protected $fillable = [
        'id_establecimiento',
        'id_user',
        'numero_identificacion',
        'nombre',
        'telefono',
        'email',
        'direccion',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
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

    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_vendedor');
    }
}
