<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use App\Models\ListaPrecio;
use Illuminate\Support\Facades\Auth;

class EstablecimientoController extends Controller
{
    public function index()
    {
        return view('admin.establecimientos.index');
    }

    public function getData(Request $request)
    {
        $establecimientos = Establecimiento::with('empresa')->get();

        return DataTables::of($establecimientos)
            ->addColumn('empresa_nombre', function ($establecimiento) {
                return $establecimiento->empresa->razon_social ?? '';
            })
            ->addColumn('logo_img', function ($establecimiento) {
                if ($establecimiento->logo) {
                    $url = asset('storage/logos_establecimientos/' . $establecimiento->logo);
                    return '<img src="' . $url . '" alt="Logo" style="height: 40px;">';
                } else {
                    return '<span class="text-muted">Sin logo</span>';
                }
            })
            ->addColumn('acciones', function ($establecimiento) {
                return '<div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-warning btn-sm editar-establecimiento mr-2" data-id="' . $establecimiento->id . '" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>';
            })
            ->editColumn('estado', function ($establecimiento) {
                return $establecimiento->estado == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado', 'logo_img'])
            ->make(true);
    }


    public function store(Request $request)
    {
        // VALIDAR PRIMERO
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre_comercial' => 'nullable|string|max:255',
            'serie' => [
                'required',
                'regex:/^\d{3}-\d{3}$/',
                Rule::unique('establecimientos')->where(function ($query) use ($request) {
                    return $query->where('empresa_id', $request->empresa_id);
                }),
            ],
            'direccion' => 'nullable|string|max:500',
            'logo' => 'nullable|mimes:jpg,jpeg|max:2048',
            'factura' => 'nullable|integer|min:1',
            'nota_credito' => 'nullable|integer|min:1',
            'nota_debito' => 'nullable|integer|min:1',
            'guia_remision' => 'nullable|integer|min:1',
            'retencion' => 'nullable|integer|min:1',
            'liquidacion_compra' => 'nullable|integer|min:1',
            'proforma' => 'nullable|integer|min:1',
            'recibo' => 'nullable|integer|min:1',
            'ingreso' => 'nullable|integer|min:1',
            'egreso' => 'nullable|integer|min:1',
            'orden_compra' => 'nullable|integer|min:1',
            'pedido' => 'nullable|integer|min:1',
            'consignacion_venta' => 'nullable|integer|min:1',
            'decimal_cantidad' => 'nullable|integer|min:0|max:6',
            'decimal_precio' => 'nullable|integer|min:0|max:6',
            'estado' => 'required|in:0,1',
        ]);

        // Procesar el logo si viene
        if ($request->hasFile('logo')) {
            try {
                $archivo = $request->file('logo');
                $nombre = uniqid('logo_') . '.' . $archivo->getClientOriginalExtension();
                $archivo->storeAs('logos_establecimientos', $nombre, 'public');

                $validated['logo'] = $nombre;
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al guardar el logo: ' . $e->getMessage(),
                ], 500);
            }
        }

        // Crear el establecimiento
        $establecimiento = Establecimiento::create($validated);

        // Crear listas de precios asociadas
        $listas = [
            ['nombre' => 'Precio general', 'descripcion' => 'Lista estándar'],
        ];

        foreach ($listas as $item) {
            ListaPrecio::firstOrCreate(
                [
                    'nombre' => $item['nombre'],
                    'id_establecimiento' => $establecimiento->id,
                ],
                [
                    'descripcion' => $item['descripcion'],
                    'id_user' => Auth::id(),
                    'estado' => true,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Establecimiento creado correctamente.'
        ]);
    }


    public function show(Establecimiento $Establecimiento)
    {
        return response()->json($Establecimiento);
    }

    public function update(Request $request, Establecimiento $establecimiento)
    {
        // Validar primero
        $validated = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'nombre_comercial' => 'nullable|string|max:255',
            'serie' => [
                'required',
                'regex:/^\d{3}-\d{3}$/',
                Rule::unique('establecimientos')->ignore($establecimiento->id)->where(function ($query) use ($request) {
                    return $query->where('empresa_id', $request->empresa_id);
                }),
            ],
            'direccion' => 'nullable|string|max:500',
            'logo' => 'nullable|mimes:jpg,jpeg|max:2048',
            'factura' => 'nullable|integer|min:1',
            'nota_credito' => 'nullable|integer|min:1',
            'nota_debito' => 'nullable|integer|min:1',
            'guia_remision' => 'nullable|integer|min:1',
            'retencion' => 'nullable|integer|min:1',
            'liquidacion_compra' => 'nullable|integer|min:1',
            'proforma' => 'nullable|integer|min:1',
            'recibo' => 'nullable|integer|min:1',
            'ingreso' => 'nullable|integer|min:1',
            'egreso' => 'nullable|integer|min:1',
            'orden_compra' => 'nullable|integer|min:1',
            'pedido' => 'nullable|integer|min:1',
            'consignacion_venta' => 'nullable|integer|min:1',
            'decimal_cantidad' => 'nullable|integer|min:0|max:6',
            'decimal_precio' => 'nullable|integer|min:0|max:6',
            'estado' => 'required|in:0,1',
        ]);

        // Procesar el logo si se ha cargado
        if ($request->hasFile('logo')) {
            $archivo = $request->file('logo');
            $nombre = uniqid('logo_') . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs('logos_establecimientos', $nombre, 'public');

            // Eliminar logo anterior si existe
            if ($establecimiento->logo && Storage::disk('public')->exists('logos_establecimientos/' . $establecimiento->logo)) {
                Storage::disk('public')->delete('logos_establecimientos/' . $establecimiento->logo);
            }

            $validated['logo'] = $nombre;
        }

        // Actualizar el establecimiento con datos validados
        $establecimiento->update($validated);

        // Asegurar que las listas de precios estén creadas
        $listas = [
            ['nombre' => 'Precio general', 'descripcion' => 'Lista estándar'],
        ];

        foreach ($listas as $item) {
            \App\Models\ListaPrecio::firstOrCreate(
                [
                    'nombre' => $item['nombre'],
                    'id_establecimiento' => $establecimiento->id,
                ],
                [
                    'descripcion' => $item['descripcion'],
                    'id_user' => Auth::id(),
                    'estado' => true,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Establecimiento actualizado correctamente.'
        ]);
    }



    public function edit($id)
    {
        $establecimiento = Establecimiento::with('empresa')->findOrFail($id);

        return response()->json($establecimiento);
    }

    public function cambiar(Request $request)
    {
        $request->validate([
            'establecimiento_id' => 'required|exists:establecimientos,id',
        ]);

        $establecimiento = Establecimiento::findOrFail($request->establecimiento_id);

        session([
            'establecimiento_id' => $establecimiento->id,
            'establecimiento_nombre' => $establecimiento->nombre_comercial,
        ]);

        // Confirmación
        return response()->json([
            'success' => true,
            'data' => session()->only(['establecimiento_id', 'establecimiento_nombre'])
        ]);
    }
}
