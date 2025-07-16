<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Menu;
use App\Models\Empresa\Establecimiento;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Super admin puede hacer todo
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Compartir datos con todas las vistas
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!$user) {
                $view->with('menus_nav', collect());
                $view->with('establecimientos_disponibles', collect());
                return;
            }

            $establecimiento_id = session('establecimiento_id');

            // Menús filtrados por establecimiento si no es super_admin
            $menus = Menu::where('activo', 1)
                ->with(['submenus' => function ($query) use ($user, $establecimiento_id) {
                    $query->where('activo', 1)
                        ->orderBy('orden');

                    if (!$user->roles->contains('name', 'super_admin') && $establecimiento_id) {
                        $query->whereIn('id', function ($subQuery) use ($user, $establecimiento_id) {
                            $subQuery->select('submenu_id')
                                ->from('submenu_establecimiento_usuario')
                                ->where('user_id', $user->id)
                                ->where('establecimiento_id', $establecimiento_id)
                                ->where('ver', true);
                        });
                    }
                }])
                ->orderBy('orden')
                ->get();

            $view->with('menus_nav', $menus);

            // Establecimientos disponibles
            if ($user->roles->contains('name', 'super_admin')) {
                $establecimientos = Establecimiento::orderBy('nombre_comercial')->get();
            } else {
                $establecimientos = Establecimiento::whereIn('id', function ($query) use ($user) {
                    $query->select('establecimiento_id')
                        ->from('establecimiento_usuario')
                        ->where('user_id', $user->id);
                })->orderBy('nombre_comercial')->get();
            }

            $view->with('establecimientos_disponibles', $establecimientos);
        });
    }
}
