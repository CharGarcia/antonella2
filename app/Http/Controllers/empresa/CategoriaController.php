<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa\Categorias\Categoria;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\SubmenuEstablecimientoUsuario;
use App\Http\Requests\CategoriaRequest;

class CategoriaController extends Controller
{
    public function index()
    {
        return view('empresa.categorias.index');
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $establecimientoId = session('establecimiento_id');
        $submenuId = session('submenu_id');

        if (!$establecimientoId || !$submenuId) {
            abort(403, 'Establecimiento o Submenú no seleccionado.');
        }

        $permisos = SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();

        $categoria = Categoria::where('id_establecimiento', $establecimientoId);
        return DataTables::eloquent($categoria)
            ->filter(function ($query) use ($request) {
                foreach ($request->columns as $index => $column) {
                    $searchValue = $column['search']['value'] ?? '';
                    if ($searchValue !== '') {
                        switch ($index) {
                            case 0:
                                $query->where('nombre', 'like', "%$searchValue%");
                                break;
                            case 1:
                                $query->where('descripcion', 'like', "%$searchValue%");
                                break;
                            case 2:
                                $query->where('estado', $searchValue);
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($categoria) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-categoria" data-id="' . $categoria->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-categoria" data-id="' . $categoria->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado', function ($categoria) {
                return $categoria->estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }


    public function store(CategoriaRequest $request)
    {
        $data = $request->validated();

        $data['id_user'] = Auth::id();
        $data['id_establecimiento'] = session('establecimiento_id');

        // 🧹 Limpieza de espacios y caracteres no deseados en el nombre
        $data['nombre'] = preg_replace('/\s+/', ' ', trim($data['nombre']));

        // Opcional: convertir a mayúsculas
        // $data['nombre'] = strtoupper($data['nombre']);

        Categoria::create($data);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(CategoriaRequest $request, Categoria $categoria)
    {
        $data = $request->validated();

        // 🧹 Limpieza del nombre
        $data['nombre'] = preg_replace('/\s+/', ' ', trim($data['nombre']));

        // Opcional: convertir a mayúsculas
        // $data['nombre'] = strtoupper($data['nombre']);

        $categoria->update($data);

        return response()->json(['message' => 'Categoría actualizada.']);
    }

    public function show(Categoria $categoria)
    {
        return response()->json($categoria);
    }

    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();

            return response()->json([
                'message' => 'Categoría eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la categoría.'
            ], 500);
        }
    }
}
