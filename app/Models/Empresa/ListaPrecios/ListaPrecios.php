<?php

namespace App\Models\Empresa\ListaPrecios;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Clientes\DatosCliente;


class ListaPrecios extends Model
{
    protected $table = 'lista_precios';

    protected $fillable = ['id_establecimiento', 'id_user', 'nombre', 'descripcion', 'estado'];

    public function clientes()
    {
        return $this->hasMany(DatosCliente::class, 'id_lista_precios');
    }
}
