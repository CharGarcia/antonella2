<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Gate;
//use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\CreateNewUser;
use Laravel\Fortify\Contracts\CreatesNewUsers;


class FortifyServiceProvider extends ServiceProvider
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
    public function boot()
    {
        // Definir la implementación para la creación de usuarios
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->cedula ?: $request->ip());
        });

        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        Fortify::authenticateUsing(function (Request $request) {
            $request->validate([
                'cedula' => ['required', 'string', 'exists:users,cedula'],
                'password' => ['required', 'string'],
            ]);

            $user = User::where('cedula', $request->cedula)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'cedula' => __('Cédula o contraseña incorrecta.'),
                ]);
            }

            // Validación de estado
            if ($user->status != 1) {
                throw ValidationException::withMessages([
                    'cedula' => __('Tu usuario está inactivo. Por favor, contacta con el administrador.'),
                ]);
            }

            return $user;
        });
    }
}
