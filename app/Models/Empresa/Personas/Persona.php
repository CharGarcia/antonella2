<?php

namespace App\Models\Empresa\Personas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresa\Establecimiento;
use App\Models\Admin\User;
use App\Models\Empresa\Clientes\DatosCliente;
use App\Models\Empresa\Vendedores\DatosVendedor;
use App\Models\Empresa\Proveedores\DatosProveedor;

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
        'tipo',
        'tipo_empresa',
        'nombre_comercial',
        'provincia',
        'ciudad',
        'pais',
        'id_banco',
        'tipo_cuenta',
        'numero_cuenta',
    ];

    protected $casts = [
        'tipo' => 'array',
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

    // Scope para clientes
    public function scopeClientes($query)
    {
        return $query->whereJsonContains('tipo', 'cliente');
    }

    public function datosCliente()
    {
        return $this->hasOne(DatosCliente::class, 'persona_id');
    }

    public function datosVendedor()
    {
        return $this->hasOne(DatosVendedor::class, 'persona_id');
    }
    public function datosProveedor()
    {
        return $this->hasOne(DatosProveedor::class, 'persona_id');
    }

    // para buscar los datos de persona

    public static function buscarPorIdentificacion($numero)
    {
        return self::where('numero_identificacion', trim($numero))->first();
    }
}
