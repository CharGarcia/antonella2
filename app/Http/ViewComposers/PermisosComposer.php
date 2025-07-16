<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\SubmenuEstablecimientoUsuario;

class PermisosComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();

        if (!$user) {
            $view->with('permisos', null);
            return;
        }

        // Obtiene el nombre de la ruta actual
        $rutaActual = Route::currentRouteName();

        // Busca el permiso asociado a esa ruta (asumiendo que la tabla tiene columna 'ruta')
        $permisos = SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->whereHas('submenu', function ($q) use ($rutaActual) {
                $q->where('ruta', $rutaActual);
            })
            ->first();

        $view->with('permisos', $permisos);
    }
}
