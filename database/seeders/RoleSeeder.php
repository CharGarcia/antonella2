<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia caché de permisos/roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guard = 'web';

        // (Opcional) Crea un set base de permisos
        $permisosBase = ['ver', 'crear', 'modificar', 'eliminar'];
        foreach ($permisosBase as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm, 'guard_name' => $guard],
                []
            );
        }

        // Crea roles si no existen
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => $guard]);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => $guard]);
        $user       = Role::firstOrCreate(['name' => 'user',        'guard_name' => $guard]);

        // Asigna permisos (ajusta a tu gusto)
        $superAdmin->syncPermissions(Permission::where('guard_name', $guard)->get());
        $admin->syncPermissions(Permission::whereIn('name', ['ver', 'crear', 'modificar'])->get());
        $user->syncPermissions(Permission::whereIn('name', ['ver'])->get());

        // Limpia caché otra vez por seguridad
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
