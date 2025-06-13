<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Storage;
use App\Models\Establecimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

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
            dd('llegó');
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

        // Crear el registro con todo incluido
        Establecimiento::create($validated);

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

        // Si sube nuevo logo, se guarda y reemplaza el anterior
        if ($request->hasFile('logo')) {
            $archivo = $request->file('logo');
            $nombre = uniqid('logo_') . '.' . $archivo->getClientOriginalExtension();
            $archivo->storeAs('logos_establecimientos', $nombre, 'public');

            // Opcional: eliminar logo anterior
            if ($establecimiento->logo && Storage::disk('public')->exists('logos_establecimientos/' . $establecimiento->logo)) {
                Storage::disk('public')->delete('logos_establecimientos/' . $establecimiento->logo);
            }

            $validated['logo'] = $nombre;
        }

        $establecimiento->update($validated);

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
        session(['establecimiento_id' => $request->establecimiento_id]);
        return response()->json(['success' => true]);
    }
}
