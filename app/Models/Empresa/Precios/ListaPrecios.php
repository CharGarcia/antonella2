<?php
namespace App\Models\Empresa\Precios;

use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Clientes\DatosCliente;
//use App\Models\Producto;

class ListaPrecios extends Model
{
    protected $table = 'lista_precios';

    protected $fillable = ['id_establecimiento', 'id_user', 'nombre', 'descripcion', 'estado'];

    public function clientes()
    {
        return $this->hasMany(DatosCliente::class, 'id_lista_precios');
    }
}
