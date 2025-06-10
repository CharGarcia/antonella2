<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::all(); // Solo necesitas los roles en la vista
        return view('admin.roles.index', compact('roles'));
    }

    public function getUsers(Request $request)
    {
        $rol = $request->input('columns.3.search.value'); // Este es el filtro por nombre del rol

        // Consulta base
        $users = User::with('roles')
            ->select('id', 'name', 'cedula', 'email');

        // Filtrar por rol si se proporciona uno
        if (!empty($rol)) {
            $users->whereHas('roles', function ($query) use ($rol) {
                $query->where('name', $rol);
            });
        }

        // Obtener todos los roles para llenar el <select>
        $roles = Role::pluck('name', 'id');

        // Retornar respuesta para DataTables
        return datatables()->of($users)
            ->addColumn('roles', function ($user) use ($roles) {
                $select = '<select class="form-control form-control-sm select-role" data-user-id="' . $user->id . '" data-previous-role="' . ($user->roles->first()->id ?? '') . '">';
                foreach ($roles as $id => $name) {
                    $selected = $user->roles->contains('id', $id) ? 'selected' : '';
                    $select .= '<option value="' . $id . '" ' . $selected . '>' . ucfirst($name) . '</option>';
                }
                $select .= '</select>';
                return $select;
            })
            ->rawColumns(['roles']) // Permite que el HTML del select se renderice
            ->make(true);
    }



    //para asignar roles al usuario
    public function assignRole(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        // Obtener el rol actual del usuario antes de cambiarlo
        $currentRole = $user->roles()->pluck('id')->first();

        // Verificar si el usuario actual es el último super_admin
        $isSuperAdmin = $user->roles()->where('name', 'super_admin')->exists();
        $superAdminCount = User::whereHas('roles', function ($query) {
            $query->where('name', 'super_admin');
        })->count();

        if ($isSuperAdmin && $superAdminCount <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Debe haber al menos un usuario con el rol de super_admin',
                'previous_role_id' => $currentRole // Enviar el ID del rol anterior
            ], 403);
        }

        // Asigna el nuevo rol
        $user->roles()->sync([$role->id]);

        return response()->json([
            'success' => true,
            'message' => 'Rol asignado correctamente'
        ]);
    }
}
