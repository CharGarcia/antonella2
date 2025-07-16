<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Personas\Persona;

class Banco extends Model
{
    use HasFactory;

    protected $table = 'bancos';

    protected $fillable = [
        'codigo',
        'nombre',
    ];

    public function personas()
    {
        return $this->hasMany(Persona::class, 'id_banco');
    }
}
