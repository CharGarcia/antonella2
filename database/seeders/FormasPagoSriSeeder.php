<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FormasPagoSriSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        DB::table('formas_pago_sri')->insert([
            [
                'codigo' => '01',
                'descripcion'  => 'SIN UTILIZACIÓN DEL SISTEMA FINANCIERO', // https://www.w3schools.com/icons/fontawesome_icons_webapp.asp
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '15',
                'descripcion'  => 'COMPENSACIÓN DE DEUDAS',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '16',
                'descripcion'  => 'TARJETA DE DÉBITO',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '17',
                'descripcion'  => 'DINERO ELECTRÓNICO',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '18',
                'descripcion'  => 'TARJETA PREPAGO',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '19',
                'descripcion'  => 'TARJETA DE CRÉDITO',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '20',
                'descripcion'  => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'codigo' => '21',
                'descripcion'  => 'ENDOSO DE TÍTULOS',
                'estado' => 'activo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
