<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
//use Spatie\Permission\PermissionRegistrar;
//use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(EmpresaSeeder::class);
        $this->call([FormasPagoSriSeeder::class]);
        $this->call([TarifaIvaSeeder::class]);
    }
}
