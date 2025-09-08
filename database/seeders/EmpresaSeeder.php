<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\Empresa;
use App\Models\Admin\Establecimiento;
use App\Models\Admin\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class EmpresaSeeder extends Seeder
{
    private int $empresasCantidad = 5;

    public function run(): void
    {
        DB::transaction(function () {
            // Mantener roles al día (opcional pero útil si usas roles en el seeder)
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

            // 1) Crear empresas
            $empresas = Empresa::factory($this->empresasCantidad)->create();

            foreach ($empresas as $empresa) {
                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Matriz',
                    serie: '001-001'
                );

                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Sucursal 002',
                    serie: '002-001'
                );

                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Sucursal 003',
                    serie: '003-001'
                );
            }
        });
    }

    private function crearEstablecimientoYAsignarUsuarios(
        Empresa $empresa,
        string $nombre,
        string $serie
    ): void {
        // Crea el establecimiento con columnas válidas
        $est = Establecimiento::create([
            'empresa_id'         => $empresa->id,
            'nombre_comercial'   => $nombre,
            'serie'              => $serie,
            'direccion'          => $empresa->direccion ?? 'Sin dirección',
            'logo'               => null,

            // Flags
            'factura'            => 1,
            'nota_credito'       => 1,
            'nota_debito'        => 1,
            'guia_remision'      => 1,
            'retencion'          => 1,
            'liquidacion_compra' => 1,
            'proforma'           => 1,
            'recibo'             => 1,
            'ingreso'            => 1,
            'egreso'             => 1,
            'orden_compra'       => 1,
            'pedido'             => 1,
            'consignacion_venta' => 1,

            // Decimales
            'decimal_cantidad'   => 2,
            'decimal_precio'     => 2,

            'estado'             => true,
        ]);

        // 🚫 No asignar nunca super_admin
        // Selecciona usuarios que NO tengan el rol super_admin
        $usuariosNoSuper = User::whereDoesntHave('roles', function ($q) {
                $q->where('name', 'super_admin')->where('guard_name', 'web');
            })
            ->inRandomOrder()
            ->limit(3)
            ->pluck('id')
            ->toArray();

        if (!empty($usuariosNoSuper)) {
            $est->usuarios()->syncWithoutDetaching($usuariosNoSuper);
        }
    }
}
