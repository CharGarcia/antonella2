<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
