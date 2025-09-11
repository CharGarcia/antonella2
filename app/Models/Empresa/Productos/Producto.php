<?php

namespace App\Models\Empresa\Productos;

use App\Models\Admin\Establecimiento;
use App\Models\Admin\User;
use App\Models\Admin\TarifaIva;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'descripcion',
        'tipo_id',
        'tarifa_iva_id',
        'precio_base',
        'estado',
        'id_establecimiento',
        'id_user'
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

    public function tarifaIva()
    {
        return $this->belongsTo(TarifaIva::class, 'tarifa_iva_id');
    }
}
