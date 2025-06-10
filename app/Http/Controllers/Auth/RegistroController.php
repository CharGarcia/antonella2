<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegistroController extends Controller
{
    public function mostrarFormulario($token)
    {
        $user = User::where('remember_token', $token)->firstOrFail();
        return view('auth.completar-registro', compact('user'));
    }

    public function guardarDatos(Request $request, $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cedula' => 'required|digits:10|unique:users,cedula',
            'password' => 'required|confirmed|min:6',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'La cédula es obligatoria.',
            'cedula.digits' => 'La cédula debe tener exactamente 10 dígitos.',
            'cedula.unique' => 'Esta cédula ya está registrada.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $user = User::where('remember_token', $token)->firstOrFail();

        // ✅ Validar si ya fue activado
        if ($user->name !== 'Pendiente' || $user->remember_token === null) {
            abort(403, 'Este enlace ya fue utilizado o el usuario ya fue activado.');
        }

        $user->update([
            'name' => $request->name,
            'cedula' => $request->cedula,
            'password' => Hash::make($request->password),
            'remember_token' => null,
        ]);

        return redirect()->route('login')->with('bienvenida', 'Tu cuenta ha sido activada correctamente.');
    }
}
