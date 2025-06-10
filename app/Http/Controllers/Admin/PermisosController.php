<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class PermisosController extends Controller
{
    public function index(Request $request)
    {
        $roles = Role::with('permissions')->get();
        return view('admin.permisos.index', compact('roles'));
    }


    //consigue los permisos
    public function getRoles(Request $request)
    {
        $data = [];
        $permisosProtegidos = ['gestionar-roles', 'gestionar-permisos'];

        // Capturar el filtro enviado por DataTables
        $rolFiltro = $request->input('columns.0.search.value');
        $permisoFiltro = $request->input('columns.1.search.value');

        $roles = Role::with('permissions')->get();

        foreach ($roles as $role) {
            // 🔍 Filtro exacto por rol
            if ($rolFiltro && strtolower($rolFiltro) !== strtolower($role->name)) {
                continue;
            }

            foreach ($role->permissions as $permission) {
                // 🔍 Filtro parcial por permiso (puedes hacerlo exacto si quieres también)
                if ($permisoFiltro && stripos($permission->name, $permisoFiltro) === false) {
                    continue;
                }

                $esProtegido = in_array($permission->name, $permisosProtegidos);

                $boton = $esProtegido
                    ? '<div class="d-flex align-items-center justify-content-center gap-2">
                    <button class="btn btn-sm btn-secondary" disabled title="Permiso protegido por el sistema">
                        <i class="fas fa-lock"></i>
                    </button>
                   </div>'
                    : '<div class="d-flex align-items-center justify-content-center gap-2">
                    <button class="btn btn-sm btn-danger btn-eliminar-permiso" title="Eliminar"
                        data-role-id="' . $role->id . '"
                        data-permission="' . $permission->name . '">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                   </div>';

                $data[] = [
                    'rol' => ucfirst($role->name),
                    'permiso' => $permission->name,
                    'accion' => $boton
                ];
            }
        }

        return datatables()->of($data)
            ->rawColumns(['permiso', 'accion'])
            ->make(true);
    }


    //para eliminar el permiso
    public function eliminarPermiso(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permission' => 'required|exists:permissions,name',
        ]);

        $role = Role::findById($request->role_id);
        $permission = Permission::findByName($request->permission);

        if (!$role->hasPermissionTo($permission)) {
            return response()->json([
                'success' => false,
                'message' => 'El permiso no está asignado al rol.'
            ], 400);
        }

        // 🔁 1. Eliminar el vínculo entre el rol y el permiso
        $role->revokePermissionTo($permission);

        // 🔁 2. Eliminar el permiso directamente de los usuarios del rol (si lo tuvieran)
        foreach ($role->users as $user) {
            if ($user->hasDirectPermission($permission)) {
                $user->revokePermissionTo($permission);
            }
        }

        // ✅ 3. Verificar si el permiso sigue en uso por otros roles o usuarios
        $permissionStillInUse =
            DB::table('role_has_permissions')->where('permission_id', $permission->id)->exists() ||
            DB::table('model_has_permissions')->where('permission_id', $permission->id)->exists();

        // ✅ 4. Eliminar el permiso solo si ya no está en uso
        if (!$permissionStillInUse) {
            $permission->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Permiso eliminado del rol. También fue eliminado del sistema si ya no estaba en uso.'
        ]);
    }

    public function guardarPermiso(Request $request)
    {
        try {
            // Buscar permiso existente
            $permiso = Permission::where('name', $request->nombrePermiso)
                ->where('guard_name', 'web')
                ->first();

            // Crear si no existe
            if (!$permiso) {
                $permiso = Permission::create([
                    'name' => $request->nombrePermiso,
                    'guard_name' => 'web',
                ]);
            }

            // Asignar a roles seleccionados
            if ($request->filled('roles')) {
                foreach ($request->roles as $rolId) {
                    $rol = Role::find($rolId);

                    if ($rol && $rol->guard_name === $permiso->guard_name) {
                        if (!$rol->hasPermissionTo($permiso)) {
                            $rol->givePermissionTo($permiso);
                        }
                    }
                }
            }


            return response()->json([
                'success' => true,
                'message' => 'Permiso asignado correctamente.'
            ]);
        } catch (\Exception $e) {
            //Log::error('Error en guardarPermiso: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ERROR: ' . $e->getMessage()
            ], 500);
        }
    }
}
