<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TarifaIvaSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('tarifa_iva')->insert([
            [
                'codigo' => '0',
                'descripcion'  => '0%',
                'porcentaje' => '0',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '2',
                'descripcion'  => '12%',
                'porcentaje' => '12',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '3',
                'descripcion'  => '14%',
                'porcentaje' => '14',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '4',
                'descripcion'  => '15%',
                'porcentaje' => '15',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '5',
                'descripcion'  => '5%',
                'porcentaje' => '5',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '6',
                'descripcion'  => 'No Objeto de Impuesto',
                'porcentaje' => '0',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '7',
                'descripcion'  => 'Exento de IVA',
                'porcentaje' => '0',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '8',
                'descripcion'  => 'IVA diferenciado',
                'porcentaje' => '8',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '10',
                'descripcion'  => '13%',
                'porcentaje' => '13',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
