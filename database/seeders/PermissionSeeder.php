<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        
        // Lista de permisos
        /* $permisos = [
            'gestionar-roles',
            'gestionar-permisos',
        ]; */

        // Crear permisos si no existen
        /* foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web',
            ]);
        } */

        // Asignar todos los permisos al rol super_admin
        /* $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'web',
        ]);

        $superAdmin->syncPermissions($permisos); */
    }
}
