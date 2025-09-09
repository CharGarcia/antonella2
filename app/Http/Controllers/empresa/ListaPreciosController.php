<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa\ListaPrecios\ListaPrecios;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\SubmenuEstablecimientoUsuario;
use App\Http\Requests\ListaPreciosRequest;

class ListaPreciosController extends Controller
{
    public function index()
    {
        return view('empresa.listaprecios.index');
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $establecimientoId = session('establecimiento_id');
        $submenuId = session('submenu_id');

        if (!$establecimientoId || !$submenuId) {
            abort(403, 'Establecimiento no seleccionado.');
        }

        $permisos = SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();

        // Query base
        $listaPrecio = ListaPrecios::query()
            ->where('id_establecimiento', $establecimientoId)
            ->select(['id', 'nombre', 'descripcion', 'estado', 'id_establecimiento']);

        return DataTables::eloquent($listaPrecio)
            ->filter(function ($q) use ($request) {
                // Filtros por columna (DataTables server-side)
                foreach ($request->input('columns', []) as $index => $column) {
                    $search = $column['search']['value'] ?? '';
                    if ($search === '') continue;

                    switch ($index) {
                        case 0: // nombre
                            $q->where('nombre', 'like', "%{$search}%");
                            break;
                        case 1: // descripcion
                            $q->where('descripcion', 'like', "%{$search}%");
                            break;
                        case 2: // estado
                            $q->where('estado', $search);
                            break;
                    }
                }
            })
            ->addColumn('acciones', function ($listaPrecio) use ($permisos) {
                $botones = '<div class="d-flex" style="gap:0.25rem;">';

                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-lista-precios" data-id="' . $listaPrecio->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-lista-precios" data-id="' . $listaPrecio->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }

                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado_badge', function ($listaPrecio) {
                return $listaPrecio->estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado_badge'])
            ->make(true);
    }

    /* public function store(ListaPreciosRequest $request)
    {
        $data = $request->validated();
        $data['id_user'] = Auth::id();
        $data['id_establecimiento'] = session('establecimiento_id');

        ListaPrecios::create($data);

        return back()->with('success', 'Lista de precios creada.');
    } */
    public function store(ListaPreciosRequest $request)
    {
        // Toma solo lo validado y descarta cualquier 'estado' que venga del cliente
        $data = $request->safe()->except('estado');

        // Seteos de servidor
        $data['estado'] = 'activo';
        $data['id_user'] = Auth::id();
        $data['id_establecimiento'] = session('establecimiento_id');

        $lp = ListaPrecios::create($data);

        // Soporte para AJAX (tu modal)
        if ($request->ajax()) {
            return response()->json([
                'id'      => $lp->id,
                'nombre'  => $lp->nombre,
                'message' => 'Lista de precios creada.'
            ]);
        }

        return back()->with('success', 'Lista de precios creada.');
    }


    public function update(ListaPreciosRequest $request, ListaPrecios $listaPrecio)
    {
        $listaPrecio->update($request->validated());
        return back()->with('success', 'Lista de precios actualizada.');
    }

    public function show(ListaPrecios $listaPrecio)
    {
        return response()->json($listaPrecio);
    }

    public function destroy(ListaPrecios $listaPrecio)
    {
        try {
            $listaPrecio->delete();
            return response()->json(['message' => 'Lista de precios eliminada correctamente.']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'No se pudo eliminar la lista de precios. Puede tener referencias.',
            ], 500);
        }
    }
}
