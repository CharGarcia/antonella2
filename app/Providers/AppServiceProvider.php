<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;

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
        //para que el usuario super_admin tenga acceso a todo
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Compartir menú con todas las vistas
        View::composer('*', function ($view) {
            $user = Auth::user();

            if (!$user) {
                $view->with('menus_nav', collect()); // Para no romper la vista si no hay usuario
                return;
            }

            $menus = Menu::where('activo', 1)
                ->with(['submenus' => function ($query) {
                    $query->where('activo', 1)
                        ->orderBy('orden');
                }])
                ->orderBy('orden')
                ->get();

            $view->with('menus_nav', $menus);
        });
    }
}
