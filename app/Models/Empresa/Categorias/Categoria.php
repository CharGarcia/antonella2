<?php

namespace App\Models\Empresa\Categorias;

use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Establecimiento;
use App\Models\Admin\User;

class Categoria extends Model
{
    // Si deseas forzar la tabla, descomenta:
    // protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'descripcion',
        'status',
        'id_user',
        'id_establecimiento',
    ];

    protected $casts = [
        'status' => 'string',
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
}
