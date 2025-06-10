<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AsignacionEstablecimientoUsuarioController extends Controller
{
    public function index()
    {
        return view('admin.asignacion_establecimiento_usuario.index');
    }

    public function getData(Request $request)
    {
        $query = DB::table('establecimiento_usuario')
            ->join('users', 'establecimiento_usuario.user_id', '=', 'users.id')
            ->join('establecimientos', 'establecimiento_usuario.establecimiento_id', '=', 'establecimientos.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['super_admin', 'admin'])
            ->select(
                'establecimiento_usuario.id',
                'users.cedula',
                'users.name as usuario',
                'users.email',
                'establecimientos.nombre_comercial',
                'establecimientos.serie'
            );

        return DataTables::of($query)
            ->addColumn('establecimiento', function ($row) {
                return $row->nombre_comercial . ' (' . $row->serie . ')';
            })
            ->filterColumn('cedula', function ($query, $keyword) {
                $query->where('users.cedula', 'like', "%{$keyword}%");
            })
            ->filterColumn('usuario', function ($query, $keyword) {
                $query->where('users.name', 'like', "%{$keyword}%");
            })
            ->filterColumn('establecimiento', function ($query, $keyword) {
                $query->where('establecimientos.nombre_comercial', 'like', "%{$keyword}%")
                    ->orWhere('establecimientos.serie', 'like', "%{$keyword}%");
            })
            ->orderColumn('usuario', function ($query, $order) {
                $query->orderBy('users.name', $order);
            })
            ->orderColumn('establecimiento', function ($query, $order) {
                $query->orderBy('establecimientos.nombre_comercial', $order)
                    ->orderBy('establecimientos.serie', $order);
            })
            ->addColumn('modulos', function ($row) {
                $nombreEstablecimiento = $row->nombre_comercial . ' (' . $row->serie . ')';
                return '<button title="Asignar módulos al usuario"
                    class="btn btn-primary btn-sm modulos-asignacion"
                    data-id="' . $row->id . '"
                    data-user="' . e($row->usuario) . '"
                    data-establecimiento="' . e($nombreEstablecimiento) . '">
                    Módulos
                </button>';
            })
            ->addColumn('eliminar', function ($row) {
                return '<button title="Eliminar establecimiento asignado"
                    class="btn btn-danger btn-sm eliminar-asignacion"
                    data-id="' . $row->id . '">
                    Eliminar
                </button>';
            })
            ->rawColumns(['modulos', 'eliminar'])
            ->make(true);
    }

    public function asignarEstablecimientos(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'establecimiento_id' => 'required|exists:establecimientos,id',
        ]);

        $yaExiste = DB::table('establecimiento_usuario')
            ->where('user_id', $request->user_id)
            ->where('establecimiento_id', $request->establecimiento_id)
            ->exists();

        if ($yaExiste) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario ya tiene asignado este establecimiento.'
            ], 422);
        }

        DB::table('establecimiento_usuario')->insert([
            'user_id' => $request->user_id,
            'establecimiento_id' => $request->establecimiento_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Establecimiento asignado correctamente al usuario.'
        ]);
    }

    public function eliminarAsignacion($id)
    {
        $relacion = DB::table('establecimiento_usuario')->where('id', $id)->first();

        if ($relacion) {
            // Verificar si el usuario de la relación tiene el rol 'admin'
            $tieneRolAdmin = DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('model_has_roles.model_id', $relacion->user_id)
                ->where('roles.name', 'admin')
                ->exists();

            if ($tieneRolAdmin) {
                // Obtener los IDs de los usuarios con rol admin vinculados a ese establecimiento
                $usuariosAdmin = DB::table('establecimiento_usuario')
                    ->join('model_has_roles', 'establecimiento_usuario.user_id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->where('roles.name', 'admin')
                    ->where('establecimiento_usuario.establecimiento_id', $relacion->establecimiento_id)
                    ->pluck('establecimiento_usuario.user_id');

                // Eliminar permisos solo de esos usuarios admin
                DB::table('submenu_establecimiento_usuario')
                    ->whereIn('user_id', $usuariosAdmin)
                    ->where('establecimiento_id', $relacion->establecimiento_id)
                    ->delete();

                // Eliminar asignaciones solo de esos usuarios admin
                DB::table('establecimiento_usuario')
                    ->whereIn('user_id', $usuariosAdmin)
                    ->where('establecimiento_id', $relacion->establecimiento_id)
                    ->delete();
            } else {
                // Eliminar solo la relación del usuario no-admin
                DB::table('submenu_establecimiento_usuario')
                    ->where('user_id', $relacion->user_id)
                    ->where('establecimiento_id', $relacion->establecimiento_id)
                    ->delete();

                DB::table('establecimiento_usuario')
                    ->where('id', $id)
                    ->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Asignación eliminada correctamente.'
        ]);
    }


    public function verPermisos($id)
    {
        $relacion = DB::table('establecimiento_usuario')->find($id);
        $submenus = DB::table('submenus')
            ->join('menus', 'submenus.menu_id', '=', 'menus.id')
            ->select('submenus.*', 'menus.nombre as menu_nombre')
            ->orderBy('menus.nombre')
            ->orderBy('submenus.nombre')
            ->get()
            ->groupBy('menu_nombre');

        $permisos = DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $relacion->user_id)
            ->where('establecimiento_id', $relacion->establecimiento_id)
            ->get()
            ->keyBy('submenu_id');

        return view('admin.asignacion_establecimiento_usuario.partials.tabla_permisos', compact('submenus', 'permisos'));
    }

    public function guardarPermisos(Request $request)
    {
        $relacion = DB::table('establecimiento_usuario')->find($request->usuario_establecimiento_id);
        $user_id = $relacion->user_id;
        $establecimiento_id = $relacion->establecimiento_id;

        DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $user_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->delete();

        foreach ($request->permisos as $submenu_id => $acciones) {
            DB::table('submenu_establecimiento_usuario')->insert([
                'user_id' => $user_id,
                'establecimiento_id' => $establecimiento_id,
                'submenu_id' => $submenu_id,
                'ver' => isset($acciones['ver']),
                'crear' => isset($acciones['crear']),
                'modificar' => isset($acciones['modificar']),
                'eliminar' => isset($acciones['eliminar']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}
