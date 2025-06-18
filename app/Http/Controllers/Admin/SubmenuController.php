<?php

namespace App\Http\Controllers\Admin;

use App\Models\Submenu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class SubmenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.submenus.index');
    }

    public function getSubmenus(Request $request)
    {
        $query = Submenu::select('submenus.*', 'menus.nombre as nombre_menu')
            ->join('menus', 'submenus.menu_id', '=', 'menus.id');

        return DataTables::of($query)
            ->filterColumn('nombre_menu', function ($query, $keyword) {
                $query->where('menus.nombre', 'like', "%{$keyword}%");
            })
            ->editColumn('nombre_menu', function ($submenu) {
                return $submenu->nombre_menu;
            })
            ->editColumn('icono', function ($submenu) {
                return '<i class="' . e($submenu->icono) . '" title="' . e($submenu->icono) . '"></i>';
            })
            ->editColumn('activo', function ($submenu) {
                return $submenu->activo
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->addColumn('acciones', function ($submenu) {
                return '
                <button class="btn btn-warning btn-sm editar-submenu" data-id="' . $submenu->id . '" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm eliminar-submenu" data-id="' . $submenu->id . '" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>';
            })
            ->rawColumns(['icono', 'activo', 'acciones'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $submenu = Submenu::create($request->all());
        return response()->json(['message' => 'Submenú creado con éxito', 'data' => $submenu]);
    }

    public function update(Request $request, Submenu $submenu)
    {
        $submenu->update($request->all());
        return response()->json(['message' => 'Submenú actualizado con éxito']);
    }

    public function show(Submenu $submenu)
    {
        return response()->json($submenu);
    }

    public function destroy(Submenu $submenu)
    {
        $submenu->delete();
        return response()->json(['message' => 'Submenú eliminado']);
    }


    public function set(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no autenticado.'], 401);
        }

        $submenuId = $request->submenu_id;
        $establecimientoId = session('establecimiento_id');

        if (!$establecimientoId) {
            return response()->json(['success' => false, 'message' => 'Establecimiento no definido en la sesión.'], 400);
        }

        // Si es super admin, permitir siempre, sino hay que darle permiso por ejemplo gestionar-clientes
        if ($user->hasRole('super_admin')) {
            session(['submenu_id' => $submenuId]);
            return response()->json(['success' => true]);
        }

        // Validar permisos para usuarios normales
        $permiso = DB::table('submenu_establecimiento_usuario')
            ->where('user_id', $user->id)
            ->where('submenu_id', $submenuId)
            ->where('establecimiento_id', $establecimientoId)
            ->first();

        if ($permiso) {
            session(['submenu_id' => $submenuId]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'No tiene permiso para acceder a este módulo.'], 403);
    }
}
