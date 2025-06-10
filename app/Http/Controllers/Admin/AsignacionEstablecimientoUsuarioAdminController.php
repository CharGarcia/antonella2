<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;


class AsignacionEstablecimientoUsuarioAdminController extends Controller
{
    public function index()
    {
        return view('admin.asignacion_establecimiento_usuario_admin.index');
    }

    public function getData(Request $request)
    {
        $adminId = Auth::id();

        // Obtener IDs de usuarios asignados a este admin
        $usuariosAsignados = DB::table('usuario_asignado')
            ->where('id_admin', $adminId)
            ->pluck('id_user');

        // Obtener datos combinando establecimientos y usuarios asignados
        $query = DB::table('establecimiento_usuario')
            ->join('users', 'establecimiento_usuario.user_id', '=', 'users.id')
            ->join('establecimientos', 'establecimiento_usuario.establecimiento_id', '=', 'establecimientos.id')
            ->whereIn('establecimiento_usuario.user_id', $usuariosAsignados)
            ->select(
                'establecimiento_usuario.id',
                'establecimiento_usuario.user_id',
                'establecimiento_usuario.establecimiento_id',
                'users.cedula',
                'users.name as usuario',
                'users.email',
                DB::raw("CONCAT(establecimientos.nombre_comercial, ' (', establecimientos.serie, ')') as establecimiento")
            );

        return DataTables::of($query)
            ->addColumn('modulos', function ($row) {
                return '<button class="btn btn-primary btn-sm modulos-asignacion-admin"
                data-user-id="' . $row->user_id . '"
                data-establecimiento-id="' . $row->establecimiento_id . '"
                data-user="' . e($row->usuario) . '"
                data-establecimiento="' . e($row->establecimiento) . '">
                Módulos
            </button>';
            })
            ->addColumn('eliminar', function ($row) {
                return '<button class="btn btn-danger btn-sm eliminar-asignacion-admin"
                data-id="' . $row->id . '" title="Eliminar asignación">
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

        $adminId = Auth::id();

        // Verifica si el admin tiene asignado el establecimiento
        $establecimientoAsignado = DB::table('establecimiento_usuario')
            ->where('user_id', $adminId)
            ->where('establecimiento_id', $request->establecimiento_id)
            ->exists();

        // Verifica si el usuario ha sido asignado a este admin (tabla usuario_asignado)
        $usuarioPermitido = DB::table('usuario_asignado')
            ->where('id_admin', $adminId)
            ->where('id_user', $request->user_id)
            ->exists();

        if (!$establecimientoAsignado || !$usuarioPermitido) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para asignar este establecimiento a este usuario.'
            ], 403);
        }

        // Verifica si ya está asignado
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

        // Asigna el establecimiento
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

        if (!$relacion) {
            return response()->json(['success' => false, 'message' => 'Relación no encontrada.'], 404);
        }

        $adminId = Auth::id();

        // Validar que el admin tenga asignado ese establecimiento
        $establecimientoAsignado = DB::table('establecimiento_usuario')
            ->where('user_id', $adminId)
            ->where('establecimiento_id', $relacion->establecimiento_id)
            ->exists();

        // Validar que el usuario esté asignado a este admin
        $usuarioPermitido = DB::table('usuario_asignado')
            ->where('id_admin', $adminId)
            ->where('id_user', $relacion->user_id)
            ->exists();

        if (!$establecimientoAsignado || !$usuarioPermitido) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta asignación.'
            ], 403);
        }

        // Eliminar permisos relacionados
        DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $relacion->user_id)
            ->where('establecimiento_id', $relacion->establecimiento_id)
            ->delete();

        // Eliminar asignación
        DB::table('establecimiento_usuario')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Asignación y permisos eliminados correctamente.'
        ]);
    }


    public function verPermisos(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'establecimiento_id' => 'required|exists:establecimientos,id',
        ]);

        $adminId = Auth::id();

        // Submenús que tiene el admin actual en esa establecimiento
        $submenusAsignados = DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $adminId)
            ->where('establecimiento_id', $request->establecimiento_id)
            ->pluck('submenu_id');

        if ($submenusAsignados->isEmpty()) {
            return response('<div class="alert alert-warning">El administrador no tiene módulos asignados en este establecimiento.</div>');
        }

        // Submenús + menú (agrupado)
        $submenus = DB::table('submenus')
            ->join('menus', 'menus.id', '=', 'submenus.menu_id')
            ->whereIn('submenus.id', $submenusAsignados)
            ->select('submenus.id as submenu_id', 'submenus.nombre', 'menus.nombre as menu_nombre')
            ->orderBy('menus.nombre')
            ->orderBy('submenus.nombre')
            ->get()
            ->groupBy('menu_nombre');

        // Permisos ya asignados al usuario destino
        $permisos = DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $request->user_id)
            ->where('establecimiento_id', $request->establecimiento_id)
            ->get()
            ->keyBy('submenu_id');

        return view('admin.asignacion_establecimiento_usuario_admin.partials.tabla_permisos', [
            'submenus' => $submenus,
            'permisos' => $permisos
        ]);
    }


    //guarda los permisos que tiene el usuario en la establecimiento
    public function guardarPermisos(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'establecimiento_id' => 'required|exists:establecimientos,id',
            'permisos' => 'array'
        ]);

        $user_id = $request->user_id;
        $establecimiento_id = $request->establecimiento_id;

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
