<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermisosHelper
{
    public static function puedeRealizarAccion(string $accion, $permisos): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        // Acceso total para super_admin
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Para admin/user se evalúa el permiso
        return !empty($permisos) && !empty($permisos->$accion);
    }


    public static function accionesDisponibles($permisos): array
    {
        $user = Auth::user();

        $acciones = [
            'ver' => false,
            'crear' => false,
            'modificar' => false,
            'eliminar' => false,
        ];

        if (!$user) {
            return $acciones;
        }

        foreach ($acciones as $accion => $valor) {
            $acciones[$accion] = $user->hasRole('super_admin') ||
                (!empty($permisos) && isset($permisos->$accion) && $permisos->$accion == 1);
        }

        return $acciones;
    }
}
