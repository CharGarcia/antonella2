<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvitacionRegistro;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\UsuarioAsignado;
use Yajra\DataTables\Facades\DataTables;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.usuarios.index');
    }

    public function getUsers(Request $request)
    {
        $authUser = Auth::user();

        $users = User::query()
            ->leftJoin('model_has_roles as mhr', function ($join) {
                $join->on('mhr.model_id', '=', 'users.id')
                    ->where('mhr.model_type', User::class);
            })
            ->leftJoin('roles', 'roles.id', '=', 'mhr.role_id')
            ->select('users.id', 'users.name', 'users.cedula', 'users.email', 'users.estado', 'roles.name as role_name');

        // Alcance por rol autenticado
        /* if ($authUser->hasRole('super_admin')) {
            // ve todo
        } elseif ($authUser->hasRole('admin')) {
            $asignados = UsuarioAsignado::where('id_admin', $authUser->id)->pluck('id_user');
            $users->whereIn('users.id', $asignados);
        } else {
            $users->where('users.id', $authUser->id);
        } */

        //$authUser = Auth::user();
        $roleName = optional($authUser)->getRoleNames()->first(); // 'super_admin' | 'admin' | 'user' | null

        if ($roleName === 'super_admin') {
            // ve todo
        } elseif ($roleName === 'admin') {
            $asignados = UsuarioAsignado::where('id_admin', $authUser->id)->pluck('id_user');
            $users->whereIn('users.id', $asignados);
        } else {
            $users->where('users.id', $authUser->id);
        }


        return DataTables::eloquent($users)
            // Búsqueda global (parcial, incluye rol)
            ->filter(function ($query) use ($request) {
                $global = trim((string) data_get($request->input(), 'search.value', ''));
                if ($global !== '') {
                    $query->where(function ($q) use ($global) {
                        $q->where('users.name', 'ilike', "%{$global}%")
                            ->orWhere('users.cedula', 'ilike', "%{$global}%")
                            ->orWhere('users.email', 'ilike', "%{$global}%")
                            ->orWhere('roles.name', 'ilike', "%{$global}%");
                    });
                }

                // Filtros por columna: 0 nombre, 1 cédula, 2 correo, 4 rol (EXACTO), 5 estado (EXACTO)
                foreach ((array) $request->input('columns', []) as $i => $col) {
                    $val = trim((string) ($col['search']['value'] ?? ''));
                    if ($val === '') continue;

                    switch ($i) {
                        case 0:
                            $query->where('users.name', 'ilike', "%{$val}%");
                            break;
                        case 1:
                            $query->where('users.cedula', 'ilike', "%{$val}%");
                            break;
                        case 2:
                            $query->where('users.email', 'ilike', "%{$val}%");
                            break;
                        case 4: // Rol asignado -> EXACTO
                            $query->where('roles.name', $val);
                            break;
                        case 5: // Estado -> EXACTO ('activo' | 'inactivo')
                            $estado = strtolower($val);
                            if (in_array($estado, ['activo', 'inactivo'], true)) {
                                $query->where('users.estado', $estado);
                            }
                            break;
                            // 3 = Reenvio (acciones) no filtra
                    }
                }
            })

            // Col 3: Reenvío (botón)
            ->addColumn('reenvio', function ($user) {
                return '
                <div class="d-flex justify-content-center">
                    <button class="btn btn-sm btn-warning reenviar-correo"
                            data-id="' . e($user->id) . '"
                            data-email="' . e($user->email) . '">
                        <i class="fas fa-envelope"></i> Reenviar
                    </button>
                </div>
            ';
            })

            // Col 4: Rol (texto, viene del JOIN)
            ->addColumn('roles', function ($user) {
                return $user->role_name ?? '';
            })

            // Col 5: Estado (select basado en string)
            ->editColumn('estado', function ($user) {
                $activo = strtolower((string) $user->estado) === 'activo';
                $colorClass = $activo ? 'bg-success text-white' : 'bg-danger text-white';
                return '
                <div class="d-flex align-items-center justify-content-center gap-2">
                    <select class="form-control form-control-sm select-estado ' . $colorClass . '" data-id="' . e($user->id) . '">
                        <option value="activo" ' . ($activo ? 'selected' : '') . '>Activo</option>
                        <option value="inactivo" ' . (!$activo ? 'selected' : '') . '>Inactivo</option>
                    </select>
                </div>
            ';
            })

            ->rawColumns(['reenvio', 'estado'])
            ->make(true);
    }

    //para cambiar el estado actvo o inactivo y ademas debe haber al menos un usuario super_admin activo
    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id'     => ['required', 'integer', 'exists:users,id'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        // Usuario a actualizar
        $user = \App\Models\Admin\User::findOrFail($data['id']);

        // Obtener el rol del usuario SIN depender de hasRole()
        $userRole = \Illuminate\Support\Facades\DB::table('model_has_roles as mhr')
            ->join('roles', 'roles.id', '=', 'mhr.role_id')
            ->where('mhr.model_id', $user->id)
            ->where('mhr.model_type', \App\Models\Admin\User::class)
            ->value('roles.name'); // 'super_admin' | 'admin' | 'user' | null

        // Si intenta inactivarse y es super_admin, verificar que exista otro super_admin ACTIVO
        if ($data['estado'] === 'inactivo' && $userRole === 'super_admin') {
            $otrosSuperAdminsActivos = \Illuminate\Support\Facades\DB::table('users')
                ->join('model_has_roles as mhr', 'mhr.model_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'mhr.role_id')
                ->where('mhr.model_type', \App\Models\Admin\User::class)
                ->where('roles.name', 'super_admin')
                ->where('users.id', '!=', $user->id)
                ->where('users.estado', 'activo') // 👈 tu estado es string
                ->count();

            if ($otrosSuperAdminsActivos === 0) {
                return response()->json([
                    'message' => 'No puedes desactivar al único super_admin del sistema.'
                ], 403);
            }
        }

        // Actualizar estado
        $user->estado = $data['estado']; // 'activo' | 'inactivo'
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
