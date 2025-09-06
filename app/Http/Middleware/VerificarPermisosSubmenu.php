<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
//use App\Models\Admin\SubmenuEstablecimientoUsuario;

class VerificarPermisosSubmenu
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Si no hay usuario o es super_admin, permitir el acceso
        if (!$user || $user->hasRole('super_admin')) {
            return $next($request);
        }

        $submenuId = session('submenu_id');
        $establecimientoId = session('establecimiento_id');

        // Verificar que la sesión tenga los IDs necesarios
        if (!$submenuId || !$establecimientoId) {
            return redirect()->route('home')->with('warning', 'No tiene permisos para acceder a este módulo.');
        }

        // Buscar el nombre del submenú para mostrarlo en el mensaje
        $submenu = \App\Models\Admin\Submenu::find($submenuId);
        $nombreSubmenu = $submenu?->nombre ?? 'este módulo';

        // Obtener los permisos del usuario en ese establecimiento y submenú
        $permisos = \App\Models\Admin\SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();

        // Si no tiene ningún permiso, redirigir con mensaje
        if (
            !$permisos ||
            !($permisos->ver || $permisos->crear || $permisos->modificar || $permisos->eliminar)
        ) {
            return redirect()->route('home')->with('warning', "No tiene permisos para acceder a {$nombreSubmenu}.");
        }

        return $next($request);
    }
}
