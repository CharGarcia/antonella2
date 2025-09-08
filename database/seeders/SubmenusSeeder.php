<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SubmenusSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        // Buscar los IDs de los menús ya creados
        $menus = DB::table('menus')->pluck('id', 'nombre');

        DB::table('submenus')->insert([
            [
                'menu_id' => $menus['Ventas'] ?? 0,
                'nombre'  => 'Categorías',
                'ruta'    => 'categorias.categorias',
                'icono'   => 'fa fa-plus',
                'orden'   => 2,
                'estado'  => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'menu_id' => $menus['Ventas'] ?? 0,
                'nombre'  => 'Vendedores',
                'ruta'    => 'vendedores.vendedores',
                'icono'   => 'fa fa-user-cog',
                'orden'   => 1,
                'estado'  => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
                        [
                'menu_id' => $menus['Ventas'] ?? 0,
                'nombre'  => 'Clientes',
                'ruta'    => 'clientes.clientes',
                'icono'   => 'fas fa-person-booth',
                'orden'   => 1,
                'estado'  => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'menu_id' => $menus['Adquisiciones'] ?? 0,
                'nombre'  => 'Compradores',
                'ruta'    => 'compradores.compradores',
                'icono'   => 'fas fa-truck',
                'orden'   => 2,
                'estado'  => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
                        [
                'menu_id' => $menus['Adquisiciones'] ?? 0,
                'nombre'  => 'Proveedores',
                'ruta'    => 'proveedores.proveedores',
                'icono'   => 'fas fa-dolly',
                'orden'   => 2,
                'estado'  => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
