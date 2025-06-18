<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * ...
 *
 * @method bool hasRole(string|array|\Spatie\Permission\Contracts\Role $roles)
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Contracts\Role ...$roles)
 * @method \Illuminate\Support\Collection getRoleNames()
 * @method $this assignRole(...$roles)
 * @method $this removeRole(...$roles)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Contracts\Permission $permission)
 */



class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'cedula',
        'email',
        'status',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    /* public function hasRole($roleName)
    {
        return $this->roles()->where('name', $roleName)->exists();
    } */


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        $role = $this->getRoleNames()->first();

        // Mapea el nombre del rol con la descripción deseada
        return match ($role) {
            'super_admin' => 'Super Administrador',
            'admin'       => 'Administrador',
            'user'        => 'Operativo',
            default       => 'Sin rol', // Si el usuario no tiene rol asignado
        };
    }

    public function adminlte_profile_url()
    {
        return 'perfil/username';
    }

    public function Establecimientos()
    {
        return $this->belongsToMany(Establecimiento::class, 'establecimiento_usuario');
    }

    public function permisosSubmenus()
    {
        return $this->hasMany(SubmenuEstablecimientoUsuario::class);
    }

    public function usuariosAsignados()
    {
        return $this->belongsToMany(
            User::class,
            'usuario_asignado',
            'id_admin',
            'id_user'
        );
    }

    public function administradores()
    {
        return $this->belongsToMany(
            User::class,
            'usuario_asignado',
            'id_user',
            'id_admin'
        );
    }
}
