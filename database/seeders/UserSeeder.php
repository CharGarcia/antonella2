<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usuario SUPER_ADMIN fijo
        $superAdmin = User::factory()->create([
            'name' => 'Super Administrador',
            'cedula' => '1717136574',
            'email' => 'superadmin@demo.com',
            'estado' => 'activo',
            'password' => bcrypt('1717136574'),
        ]);
        $superAdmin->assignRole('super_admin');

        // 4 usuarios ADMIN
        $admins = User::factory(4)->create([
            'estado' => 'activo',
            'password' => bcrypt('admin123'),
        ]);
        foreach ($admins as $admin) {
            $admin->assignRole('admin');
        }

        // 5 usuarios USER
        $users = User::factory(5)->create([
            'estado' => 'activo',
            'password' => bcrypt('user123'),
        ]);
        foreach ($users as $user) {
            $user->assignRole('user');
        }
    }
}
