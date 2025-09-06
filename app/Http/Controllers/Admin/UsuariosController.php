<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitacionRegistro;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\UsuarioAsignado;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.usuarios.index');
    }


    public function getUsers(Request $request)
    {
        $rol = $request->input('columns.3.search.value'); // Filtro por rol
        $authUser = Auth::user();

        $users = User::with('roles:id,name')
            ->select('id', 'name', 'cedula', 'email', 'estado');

        // Lógica de acceso según el rol del usuario autenticado
        if ($authUser->hasRole('super_admin')) {
            // No se aplica ningún filtro adicional, puede ver todos los usuarios
        } elseif ($authUser->hasRole('admin')) {
            // Filtrar solo los usuarios asignados a este admin
            $asignados = UsuarioAsignado::where('id_admin', $authUser->id)->pluck('id_user');
            $users->whereIn('id', $asignados);
        }

        if (!empty($rol)) {
            $users->whereHas('roles', function ($query) use ($rol) {
                $query->where('name', $rol);
            });
        }

        $estado = $request->input('columns.4.search.value');
        if ($estado !== null && $estado !== '') {
            $users->where('estado', $estado);
        }

        return datatables()->of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->join(', ');
            })
            ->addColumn('estado', function ($user) {
                $colorClass = $user->estado ? 'bg-success text-white' : 'bg-danger text-white';
                $selectedActivo = $user->estado ? 'selected' : '';
                $selectedInactivo = !$user->estado ? 'selected' : '';

                return '
        <div class="d-flex align-items-center justify-content-center gap-2">
            <select class="form-control form-control-sm select-estado ' . $colorClass . '" data-id="' . $user->id . '">
                <option value="activo" ' . $selectedActivo . '>Activo</option>
                <option value="inactivo" ' . $selectedInactivo . '>Inactivo</option>
            </select>
        </div>
    ';
            })
            ->addColumn('acciones', function ($user) {
                return '
        <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-sm btn-warning reenviar-correo" data-id="' . $user->id . '" data-email="' . $user->email . '">
                <i class="fas fa-envelope"></i> Reenviar
            </button>
        </div>';
            })
            ->rawColumns(['roles', 'estado', 'acciones'])
            ->make(true);
    }

    //para cambiar el estado actvo o inactivo y ademas debe haber al menos un usuario super_admin activo

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id'     => ['required', 'integer', 'exists:users,id'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $user = \App\Models\Admin\User::findOrFail($data['id']);

        // 🚨 Si el usuario es super_admin y se intenta inactivar, verificamos
        if ($data['estado'] === 'inactivo' && $user->hasRole('super_admin')) {
            $otrosSuperAdmins = \App\Models\Admin\User::role('super_admin')
                ->where('id', '!=', $user->id)
                ->where('estado', 1) // activos
                ->count();

            if ($otrosSuperAdmins === 0) {
                return response()->json([
                    'message' => 'No puedes desactivar al único super_admin del sistema.'
                ], 403);
            }
        }

        // ✅ Actualizar estado normalmente
        $user->estado = $data['estado'] === 'activo' ? 1 : 0;
        $user->save();

        return response()->json(['message' => 'Estado actualizado correctamente.']);
    }


    public function asignarEmpresas(Request $request, User $user)
    {
        $request->validate([
            'empresas' => 'required|array',
            'empresas.*' => 'exists:empresas,id',
        ]);

        $user->empresas()->sync($request->empresas);

        return back()->with('success', 'Empresas asignadas correctamente');
    }

    //para crear un usuario desde adentro del sistema cuando el usuario esta logueado
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email',
        ], [
            'email.required' => 'El campo correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.max' => 'El correo electrónico no debe superar los 255 caracteres.',
            'email.unique' => 'Este correo electrónico ya está registrado en el sistema.',
        ]);

        $token = Str::random(60);

        $user = User::create([
            'name' => 'Pendiente',
            'cedula' => '0000000000',
            'email' => $request->email,
            'password' => bcrypt(Str::random(10)),
            'remember_token' => $token,
        ]);

        $user->assignRole('user');

        // Registrar asignación
        UsuarioAsignado::create([
            'id_admin' => Auth::id(),
            'id_user' => $user->id,
        ]);

        Mail::to($user->email)->send(new InvitacionRegistro($user, $token));

        return response()->json([
            'message' => 'Usuario creado. Se envió un correo para confirmar su registro.',
        ]);
    }

    public function reenviarCorreo(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $id,
        ]);

        $user = User::findOrFail($id);

        // Verificar que aún no se haya registrado (basado en nombre y cédula temporales)
        if ($user->name !== 'Pendiente' || $user->cedula !== '0000000000') {
            return response()->json([
                'message' => 'El usuario ya ha completado su registro. No se puede reenviar ni cambiar el correo.'
            ], 400);
        }

        // Cambiar el correo si es diferente
        if ($user->email !== $request->email) {
            $user->email = $request->email;
        }

        // Generar o conservar el token
        $token = $user->remember_token ?? Str::random(60);
        $user->remember_token = $token;
        $user->save();

        // Enviar el correo
        Mail::to($user->email)->send(new InvitacionRegistro($user, $token));

        return response()->json([
            'message' => 'Correo reenviado correctamente.'
        ]);
    }
}
