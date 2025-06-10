<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'cedula' => ['required', 'string', 'max:10', 'unique:users'], // Usando cédula en lugar de email
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        $creadoPor = null;
        if (app()->runningInConsole() === false && request()->user()) {
            $creadoPor = request()->user()->id;
        }


        // Crear usuario
        $user = User::create([
            'name' => $input['name'],
            'cedula' => $input['cedula'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        //al crear el primer usuario se crean los roles
        if (User::count() === 1) {
            Role::create(['name' => 'super_admin']);
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'user']);
            $user->assignRole('super_admin');
        } else {
            $user->assignRole('user');
        }

        return $user;
    }
}
