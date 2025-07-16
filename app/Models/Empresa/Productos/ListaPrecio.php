<?php

namespace App\Models\Empresa\Productos;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Establecimiento;
use App\Models\Admin\User;

class ListaPrecio extends Model
{
    protected $table = 'lista_precios';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'id_establecimiento',
        'id_user',
    ];

    // Relaciones opcionales
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'id_establecimiento');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
