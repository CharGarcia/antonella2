<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Empresa\Productos\Producto;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Admin\SubmenuEstablecimientoUsuario;
use App\Http\Requests\ProductoRequest;
use App\Models\Admin\TarifaIva;

class ProductoController extends Controller
{
    public function index()
    {
        $tarifaIva = TarifaIva::where('estado', 'activo')
            ->orderBy('descripcion')
            ->pluck('descripcion', 'codigo');
        return view('empresa.productos.index', compact('tarifaIva'));
    }


    public function obtenerSiguienteCodigo()
    {
        $establecimientoId = session('establecimiento_id');

        // Obtener el último código del establecimiento
        $ultimo = Producto::where('id_establecimiento', $establecimientoId)
            ->orderBy('codigo', 'desc')
            ->value('codigo');

        if (!$ultimo) {
            return response()->json(['codigo' => '001']);
        }

        // Buscar el segmento numérico al final del código
        if (preg_match('/^(.*?)(\d+)$/', $ultimo, $matches)) {
            $prefijo = $matches[1]; // todo lo que no es número
            $numero  = $matches[2]; // solo la parte numérica
            $siguienteNumero = str_pad(((int)$numero + 1), strlen($numero), '0', STR_PAD_LEFT);
            $nuevoCodigo = $prefijo . $siguienteNumero;
        } else {
            // Si no se encontró número, agrega -001
            $nuevoCodigo = $ultimo . '-001';
        }

        return response()->json(['codigo' => $nuevoCodigo]);
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

        $datos_consulta = Producto::with('tarifaIva')->where('id_establecimiento', $establecimientoId);

        $tipos = [
            '1' => 'Producto',
            '2' => 'Servicio',
            '3' => 'Activo fijo',
            '4' => 'Kit/combo',
            '5' => 'Bien no inventariable',
        ];

        return DataTables::eloquent($datos_consulta)
            ->filter(function ($query) use ($request, $tipos) {
                foreach ($request->columns as $column) {
                    $searchValue = $column['search']['value'] ?? '';
                    $columnName = $column['name'] ?? '';

                    if ($searchValue !== '') {
                        switch ($columnName) {
                            case 'codigo':
                                $query->where('codigo', 'like', "%$searchValue%");
                                break;
                            case 'descripcion':
                                $query->where('descripcion', 'like', "%$searchValue%");
                                break;
                            case 'estado':
                                $query->where('estado', $searchValue);
                                break;
                            case 'tipo':
                                $tipoId = array_search($searchValue, $tipos);
                                if ($tipoId !== false) {
                                    $query->where('tipo_id', $tipoId);
                                }
                                break;
                            case 'tarifa_iva':
                                $query->whereHas('tarifaIva', function ($q) use ($searchValue) {
                                    $q->where('descripcion', 'like', "%$searchValue%");
                                });
                                break;
                        }
                    }
                }
            })

            ->addColumn('acciones', function ($datos_consulta) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-producto" data-id="' . $datos_consulta->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-producto" data-id="' . $datos_consulta->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('tarifa_iva', function ($datos_consulta) {
                return $datos_consulta->tarifaIva->descripcion ?? '-';
            })
            ->addColumn('estado', function ($datos_consulta) {
                return $datos_consulta->estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })

            ->addColumn('tipo', function ($producto) use ($tipos) {
                return $tipos[$producto->tipo_id] ?? '-';
            })

            ->addColumn('precio_base', function ($producto) {
                return '<div class="text-right">' . number_format($producto->precio_base, 6) . '</div>';
            })

            ->rawColumns(['acciones', 'estado', 'tarifa_iva', 'tipo', 'precio_base'])
            ->make(true);
    }

    public function store(ProductoRequest $request)
    {
        $data = $request->validated();

        $data['id_user'] = Auth::id();
        $data['id_establecimiento'] = session('establecimiento_id');

        // 🧹 Limpieza
        $data['codigo'] = strtoupper(trim($data['codigo']));
        $data['descripcion'] = preg_replace('/\s+/', ' ', trim($data['descripcion']));

        Producto::create($data);

        return back()->with('success', 'Producto creado.');
    }


    public function show(Producto $producto)
    {
        return response()->json($producto);
    }

    public function update(ProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();

        // 🧹 Limpieza adicional
        $data['codigo'] = trim($data['codigo']);
        $data['descripcion'] = trim($data['descripcion']);

        $producto->update($data);

        return response()->json(['message' => 'Producto actualizado.']);
    }


    public function destroy(Producto $producto)
    {
        try {
            $producto->delete();

            return response()->json([
                'message' => 'Producto eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar el Producto.'
            ], 500);
        }
    }
}
