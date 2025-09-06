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
            // Asegura que exista el rol (no obligatorio, pero ayuda si luego buscas por rol)
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
            Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

            // 1) Crear empresas
            $empresas = Empresa::factory($this->empresasCantidad)->create();

            // 2) Buscar super admin si existe (por rol o por email fallback)
            $superAdmin = User::whereHas('roles', fn($q) =>
                $q->where('name', 'super_admin')->where('guard_name', 'web')
            )->first() ?: User::where('email', 'superadmin@demo.com')->first();

            foreach ($empresas as $empresa) {
                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Matriz',
                    serie: '001-001',
                    superAdmin: $superAdmin
                );

                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Sucursal 002',
                    serie: '002-001',
                    superAdmin: $superAdmin
                );

                $this->crearEstablecimientoYAsignarUsuarios(
                    $empresa,
                    nombre: $empresa->razon_social.' - Sucursal 003',
                    serie: '003-001',
                    superAdmin: $superAdmin
                );
            }
        });
    }

    private function crearEstablecimientoYAsignarUsuarios(
        Empresa $empresa,
        string $nombre,
        string $serie,
        ?User $superAdmin = null
    ): void {
        // Crea el establecimiento con columnas que SÍ existen en tu tabla
        $est = Establecimiento::create([
            'empresa_id'         => $empresa->id,
            'nombre_comercial'   => $nombre,
            'serie'              => $serie,
            'direccion'          => $empresa->direccion ?? 'Sin dirección',
            'logo'               => null,

            // Flags (tu tabla tiene DEFAULT 1, pero los dejamos explícitos por claridad)
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

            // Decimales (tu tabla tiene DEFAULT 2)
            'decimal_cantidad'   => 2,
            'decimal_precio'     => 2,

            'estado'             => true,
        ]);

        // Adjuntar usuarios al pivote establecimiento_usuario
        $attachIds = [];
        if ($superAdmin) {
            $attachIds[] = $superAdmin->id;
        }

        $randomUsers = User::inRandomOrder()->limit(3)->pluck('id')->toArray();
        $attachIds = array_unique(array_merge($attachIds, $randomUsers));

        $est->usuarios()->syncWithoutDetaching($attachIds);
    }
}
