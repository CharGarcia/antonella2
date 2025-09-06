<?php

namespace App\Providers;

use App\Models\Admin\User;
use App\Models\Admin\Establecimiento;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use App\Providers\RouteServiceProvider;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CreatesNewUsers::class, CreateNewUser::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Límite de intentos de login
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->cedula ?: $request->ip());
        });

        // Vistas personalizadas
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // Lógica de autenticación personalizada
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

            if ($user->estado != 'activo') {
                throw ValidationException::withMessages([
                    'cedula' => __('Tu usuario está inactivo. Por favor, contacta con el administrador.'),
                ]);
            }

            return $user;
        });

        // Asignar establecimiento automáticamente al iniciar sesión
        Event::listen(Login::class, function ($event) {
            $user = $event->user;

            if ($user->hasRole('super_admin')) {
                $establecimiento = Establecimiento::orderBy('nombre_comercial')->first();
            } else {
                $establecimiento = Establecimiento::whereIn('id', function ($query) use ($user) {
                    $query->select('establecimiento_id')
                        ->from('establecimiento_usuario')
                        ->where('user_id', $user->id);
                })->orderBy('nombre_comercial')->first();
            }

            if ($establecimiento) {
                session(['establecimiento_id' => $establecimiento->id]);
            }
        });
    }
}
