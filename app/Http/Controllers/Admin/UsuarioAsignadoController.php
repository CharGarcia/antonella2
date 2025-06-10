<?php

namespace App\Http\Controllers\Admin;

use App\Models\UsuarioAsignado;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class UsuarioAsignadoController extends Controller
{
    public function index()
    {
        return view('admin.usuario_asignado.index');
    }

    public function getData(Request $request)
    {
        $query = DB::table('usuario_asignado')
            ->join('users as u', 'usuario_asignado.id_user', '=', 'u.id')
            ->join('users as a', 'usuario_asignado.id_admin', '=', 'a.id') // join con admin
            ->select([
                'usuario_asignado.id',
                'u.cedula as cedula',
                'u.email as email',
                'u.name as usuario',
                'a.name as admin' // nombre del administrador
            ]);

        return DataTables::of($query)
            ->addColumn('accion', function ($row) {
                return '<button data-id="' . $row->id . '" title ="Eliminar" class="btn btn-danger btn-sm btn-eliminar">
                        <i class="fas fa-trash"></i>
                    </button>';
            })
            ->rawColumns(['accion'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'admin_id' => 'required|exists:users,id',
        ]);

        // Validar si ya existe esa relación
        $existe = UsuarioAsignado::where('id_admin', $request->admin_id)
            ->where('id_user', $request->user_id)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Este usuario ya está asignado a este administrador.'
            ], 409); // 409 Conflict
        }

        UsuarioAsignado::create([
            'id_admin' => $request->admin_id,
            'id_user'  => $request->user_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuario asignado correctamente.'
        ]);
    }



    public function destroy($id)
    {
        $relacion = DB::table('usuario_asignado')->where('id', $id)->first();

        if (!$relacion) {
            return response()->json(['message' => 'Asignación no encontrada.'], 404);
        }

        DB::table('usuario_asignado')->where('id', $id)->delete();

        return response()->json(['message' => 'Asignación eliminada correctamente.']);
    }
}
