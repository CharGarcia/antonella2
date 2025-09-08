<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MenusSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('menus')->insert([
            [
                'nombre' => 'Dashboard',
                'icono'  => 'fa fa-home', // https://www.w3schools.com/icons/fontawesome_icons_webapp.asp
                'orden'  => 1,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Nómina',
                'icono'  => 'fas fa-book-reader',
                'orden'  => 2,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'SRI',
                'icono'  => 'fas fa-stamp',
                'orden'  => 3,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Tesorería',
                'icono'  => 'fas fa-piggy-bank',
                'orden'  => 4,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Contabilidad',
                'icono'  => 'fa fa-calculator',
                'orden'  => 5,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Ventas',
                'icono'  => 'fa fa-shopping-cart',
                'orden'  => 6,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Adquisiciones',
                'icono'  => 'fa fa-shopping-bag',
                'orden'  => 7,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Reportes',
                'icono'  => 'fas fa-file-invoice',
                'orden'  => 8,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Configuración',
                'icono'  => 'fa fa-cog',
                'orden'  => 9,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Inventario',
                'icono'  => 'fas fa-clipboard-list',
                'orden'  => 10,
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
