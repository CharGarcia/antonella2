<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'razon_social',
        'tipo_contribuyente',
        'ruc',
        'regimen',
        'contabilidad',
        'contribuyente_especial',
        'agente_retencion',
        'nombre_rep_leg',
        'cedula_rep_leg',
        'nombre_contador',
        'ruc_contador',
        'email',
        'telefono',
        'direccion',
        'archivo_firma',
        'password_firma',
        'estado',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'establecimiento_usuario');
    }

}
