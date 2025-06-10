<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitacionRegistro;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.usuarios.index');
    }

    public function getUsers(Request $request)
    {
        $rol = $request->input('columns.3.search.value'); // Filtro por rol

        $users = User::with('roles:id,name')
            ->select('id', 'name', 'cedula', 'email', 'status');

        if (!empty($rol)) {
            $users->whereHas('roles', function ($query) use ($rol) {
                $query->where('name', $rol);
            });
        }

        $status = $request->input('columns.4.search.value');
        if ($status !== null && $status !== '') {
            $users->where('status', $status);
        }


        return datatables()->of($users)
            ->addColumn('roles', function ($user) {
                return $user->roles->pluck('name')->join(', ');
            })
            ->addColumn('status', function ($user) {
                $checked = $user->status ? 'checked' : '';
                $label = $user->status ? 'Activo' : 'Inactivo';
                $color = $user->status ? 'text-success' : 'text-danger';
                return '
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div class="form-check form-switch mr-1">
                            <input class="form-check-input toggle-status" type="checkbox" data-id="' . $user->id . '" ' . $checked . '>
                        </div>
                        <span class="status-label ' . $color . ' fw-bold">' . $label . '</span>
                    </div>';
            })
            ->rawColumns(['roles', 'status'])
            ->make(true);
    }


    public function updateStatus(Request $request)
    {
        $user = User::findOrFail($request->id);
        $nuevoEstado = (bool) $request->status;

        // Validación: Si está intentando desactivar un super_admin
        if ($user->hasRole('super_admin') && $nuevoEstado === false) {
            $superAdminsActivos = User::whereHas('roles', function ($q) {
                $q->where('name', 'super_admin');
            })
                ->where('status', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($superAdminsActivos === 0) {
                return response()->json([
                    'message' => 'Debe haber al menos un usuario super_admin activo.',
                ], 403);
            }
        }

        // Si pasa la validación, actualiza el estado
        $user->status = $nuevoEstado;
        $user->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente.',
            'status' => $user->status,
        ]);
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
            'name' => 'Pendiente',                  // valor temporal
            'cedula' => '0000000000',               // valor temporal
            'email' => $request->email,
            'password' => bcrypt(Str::random(10)),  // contraseña temporal
            'remember_token' => $token,
        ]);


        $user->assignRole('user');

        Mail::to($user->email)->send(new InvitacionRegistro($user, $token));

        return response()->json([
            'message' => 'Usuario creado. Se envió un correo para confirmar su registro.',
        ]);
    }
}
