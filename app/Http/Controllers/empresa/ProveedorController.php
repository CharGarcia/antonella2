<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Empresa\Personas\Persona;
use App\Models\Empresa\Proveedores\DocumentoProveedor;
use App\Models\Admin\FormasPagoSri;
use App\Models\Admin\Banco;
use App\Models\Admin\RetencionSri;
use Illuminate\Support\Carbon;


class ProveedorController extends Controller
{
    public function index()
    {
        $compradores = Persona::query()
            ->where('id_establecimiento', session('establecimiento_id'))
            ->whereJsonContains('tipo', ['comprador'])
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        $formasPago = FormasPagoSri::pluck('descripcion', 'codigo'); // O el campo adecuado
        $bancos = Banco::where('estado', '1')
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        $retencionRenta = RetencionSri::where('estado', 'activo')
            ->where('impuesto', 'renta')
            ->orderBy('concepto')
            ->pluck('concepto', 'id');

        $retencionIva = RetencionSri::where('estado', 'activo')
            ->where('impuesto', 'iva')
            ->orderBy('concepto')
            ->pluck('concepto', 'id');

        return view('empresa.proveedores.index', compact('formasPago', 'bancos', 'retencionRenta', 'retencionIva', 'compradores')); //, 'compradores'
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $establecimientoId = session('establecimiento_id');
        $submenuId = session('submenu_id');

        $permisos = \App\Models\Admin\SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();

        // Incluye la relación con datosProveedor para filtrar y mostrar estado
        $proveedores = Persona::where('id_establecimiento', $establecimientoId)
            ->whereJsonContains('tipo', ['proveedor'])
            ->with('datosProveedor');

        return DataTables::eloquent($proveedores)
            ->filter(function ($query) use ($request) {
                foreach ($request->columns as $index => $column) {
                    $searchValue = $column['search']['value'] ?? '';
                    if ($searchValue !== '') {
                        switch ($index) {
                            case 0:
                                $query->where('nombre', 'like', "%$searchValue%");
                                break;
                            case 1:
                                $query->where('numero_identificacion', 'like', "%$searchValue%");
                                break;
                            case 2:
                                $query->where('telefono', 'like', "%$searchValue%");
                                break;
                            case 3:
                                $query->where('email', 'like', "%$searchValue%");
                                break;
                            case 4:
                                $query->where('direccion', 'like', "%$searchValue%");
                                break;
                            case 5:
                                $estado = strtolower($searchValue);
                                if (in_array($estado, ['activo', 'inactivo'])) {
                                    $query->whereHas('datosProveedor', function ($q) use ($estado) {
                                        $q->where('estado', $estado);
                                    });
                                }
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($proveedor) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-proveedor" data-id="' . $proveedor->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-proveedor" data-id="' . $proveedor->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado', function ($proveedor) {
                $estado = $proveedor->datosProveedor->estado ?? 'activo';
                return $estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }


    public function destroy($id)
    {
        $proveedor = Persona::findOrFail($id);

        if (!in_array('proveedor', $proveedor->tipo ?? [])) {
            return response()->json(['message' => 'No es un proveedor válido.'], 400);
        }

        $tipos = array_filter($proveedor->tipo, fn($tipo) => $tipo !== 'proveedor');

        if (!empty($tipos)) {
            $proveedor->update(['tipo' => array_values($tipos)]);
        } else {
            $proveedor->delete();
        }

        return response()->json(['message' => 'Proveedor eliminado']);
    }

    public function store(Request $request)
    {
        $messages = [
            'tipo_identificacion.required' => 'El tipo de identificación es obligatorio.',
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'nombre.required' => 'El nombre del proveedor es obligatorio.',
            'email.required' => 'El email es obligatorio.'
        ];

        $validator = Validator::make($request->all(), [
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => 'required|string',
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:10',
            'email' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'tipo_empresa' => 'nullable|string',
            'nombre_comercial' => 'nullable|string',
            'id_banco' => 'nullable|string',
            'tipo_cuenta' => 'nullable|string',
            'numero_cuenta' => 'nullable|string',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:50',

            // datos_proveedor
            'codigo_interno' => 'nullable|string',
            'categoria_proveedor' => 'nullable|string',
            'segmento' => 'nullable|string',
            'comprador_asignado' => 'nullable|string',
            'zona' => 'nullable|string',
            'clasificacion' => 'nullable|string',
            'inicio_relacion' => 'nullable|date_format:d/m/Y',
            'estado' => 'required|in:activo,inactivo',
            'configuracion_especial' => 'nullable|array',

            // configuración
            'notas' => 'nullable|string',

            // financieros
            'limite_credito' => 'nullable|numeric',
            'dias_credito' => 'nullable|integer',
            'forma_pago' => 'nullable|string',
            'observaciones_crediticias' => 'nullable|string',
            'historial_pagos' => 'nullable|array',
            'nivel_riesgo' => 'nullable|string',

            // tributarios
            'agente_retencion' => 'nullable|boolean',
            'contribuyente_especial' => 'nullable|boolean',
            'obligado_contabilidad' => 'nullable|boolean',
            'parte_relacionada' => 'nullable|boolean',
            'regimen_tributario' => 'nullable|string',
            'codigo_tipo_proveedor_sri' => 'nullable|string',
            'retencion_fuente' => 'nullable|string',
            'retencion_iva' => 'nullable|string',

            // kpi
            'total_compras_anual' => 'nullable|numeric',
            'cantidad_facturas' => 'nullable|integer',
            'monto_promedio_compra' => 'nullable|numeric',
            'ultima_compra_fecha' => 'nullable|date_format:d/m/Y',
            'ultima_compra_monto' => 'nullable|numeric',
            'dias_promedio_pago' => 'nullable|integer',
            'porcentaje_entregas_a_tiempo' => 'nullable|numeric',
            'porcentaje_entregas_fuera_plazo' => 'nullable|numeric',
            'porcentaje_devoluciones' => 'nullable|numeric',
            'porcentaje_reclamos' => 'nullable|numeric',
            'cantidad_incidentes' => 'nullable|integer',
            'saldo_por_pagar' => 'nullable|numeric',
            'promedio_mensual' => 'nullable|numeric',
            'productos_frecuentes' => 'nullable|array',

            // documentos
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,zip|max:5120',
            'tipos.*' => 'nullable|string',

            // contables
            'cuenta_por_pagar' => 'nullable|string|max:20',
            'cuenta_gasto_predeterminada' => 'nullable|string|max:20',
            'cuenta_inventario_predeterminada' => 'nullable|string|max:20',
            'cuenta_anticipo' => 'nullable|string|max:20',
            'centro_costo' => 'nullable|string|max:20',
            'proyecto' => 'nullable|string|max:20'
        ], $messages);

        $validator->after(function ($v) use ($request) {
            $emails = array_map('trim', explode(',', $request->input('email')));
            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $v->errors()->add('email', "El correo \"$email\" no es válido.");
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['inicio_relacion'])) {
            $data['inicio_relacion'] = Carbon::createFromFormat('d/m/Y', $data['inicio_relacion'])
                ->format('Y-m-d');
        }

        if (!empty($data['ultima_compra_fecha'])) {
            $data['ultima_compra_fecha'] = Carbon::createFromFormat('d/m/Y', $data['ultima_compra_fecha'])
                ->format('Y-m-d');
        }

        try {
            DB::transaction(function () use ($data, $request) {
                $data['email'] = !empty($data['email'])
                    ? implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))))
                    : null;

                foreach (['nombre', 'direccion', 'provincia', 'ciudad', 'pais', 'nombre_comercial'] as $campo) {
                    if (!empty($data[$campo])) {
                        $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
                    }
                }

                $persona = Persona::where('numero_identificacion', trim($data['numero_identificacion']))
                    ->where('id_establecimiento', session('establecimiento_id'))
                    ->first();

                if ($persona) {
                    $tipos = $persona->tipo ?? [];
                    if (!in_array('proveedor', $tipos)) {
                        $tipos[] = 'proveedor';
                        $persona->tipo = $tipos;
                        $persona->save();
                    }
                } else {
                    $persona = Persona::create(array_merge(
                        $data,
                        [
                            'id_user' => Auth::id(),
                            'id_establecimiento' => session('establecimiento_id'),
                            'tipo' => ['proveedor']
                        ]
                    ));
                }

                $datosProveedor = $persona->datosProveedor;
                if (!$datosProveedor) {
                    $datosProveedor = $persona->datosProveedor()->create([
                        'codigo_interno' => $data['codigo_interno'] ?? null,
                        'categoria_proveedor' => $data['categoria_proveedor'] ?? null,
                        'segmento' => $data['segmento'] ?? null,
                        'fecha_registro' => Carbon::now(),
                        'comprador_asignado' => $data['comprador_asignado'] ?? null,
                        'zona' => $data['zona'] ?? null,
                        'provincia' => $data['provincia'] ?? null,
                        'ciudad' => $data['ciudad'] ?? null,
                        'pais' => $data['pais'] ?? null,
                        'clasificacion' => $data['clasificacion'] ?? null,
                        'inicio_relacion' => $data['inicio_relacion'] ?? null,
                        'estado' => $data['estado'],
                        'tipo_empresa' => $data['codigo_tipo_proveedor_sri'] ?? null,
                        'id_banco ' => $data['id_banco '] ?? null,
                        'tipo_cuenta' => $data['tipo_cuenta'] ?? null,
                        'numero_cuenta' => $data['numero_cuenta'] ?? null,
                        'configuracion_especial' => $data['configuracion_especial'] ?? null,
                    ]);

                    $datosProveedor->configuracion()->create([
                        'notas' => $data['notas'] ?? null,
                    ]);

                    $datosProveedor->financieros()->create([
                        'limite_credito' => $data['limite_credito'] ?? null,
                        'dias_credito' => $data['dias_credito'] ?? null,
                        'forma_pago' => $data['forma_pago'] ?? null,
                        'observaciones_crediticias' => $data['observaciones_crediticias'] ?? null,
                        'historial_pagos' => !empty($data['historial_pagos']) ? $data['historial_pagos'] : null,
                        'nivel_riesgo' => $data['nivel_riesgo'] ?? null,
                    ]);

                    $datosProveedor->tributarios()->create([
                        'agente_retencion' => $data['agente_retencion'] ?? false,
                        'contribuyente_especial' => $data['contribuyente_especial'] ?? false,
                        'obligado_contabilidad' => $data['obligado_contabilidad'] ?? false,
                        'parte_relacionada' => $data['parte_relacionada'] ?? false,
                        'regimen_tributario' => $data['regimen_tributario'] ?? null,
                        'codigo_tipo_proveedor_sri' => $data['codigo_tipo_proveedor_sri'] ?? null,
                        'retencion_fuente' => $data['retencion_fuente'] ?? null,
                        'retencion_iva' => $data['retencion_iva'] ?? null,
                    ]);

                    if ($request->hasFile('documentos')) {
                        $tipos = $request->input('tipos', []);
                        foreach ($request->file('documentos') as $index => $archivo) {
                            $tipo = $tipos[$index] ?? null;
                            $ruta = $archivo->store("proveedores/documentos", 'public');
                            $datosProveedor->documentos()->create([
                                'tipo' => $tipo,
                                'archivo' => $ruta
                            ]);
                        }
                    }

                    $datosProveedor->historial()->create([
                        'descripcion' => 'Proveedor creado por el usuario ID: ' . Auth::id(),
                        'tipo' => 'creacion',
                        'fecha' => now(),
                    ]);

                    $datosProveedor->kpi()->create([
                        'total_compras_anual' => $data['total_compras_anual'] ?? 0,
                        'cantidad_facturas' => $data['cantidad_facturas'] ?? 0,
                        'monto_promedio_compra' => $data['monto_promedio_compra'] ?? 0,
                        'ultima_compra_fecha' => $data['ultima_compra_fecha'] ?? null,
                        'ultima_compra_monto' => $data['ultima_compra_monto'] ?? 0,
                        'dias_promedio_pago' => $data['dias_promedio_pago'] ?? 0,
                        'porcentaje_entregas_a_tiempo' => $data['porcentaje_entregas_a_tiempo'] ?? 0,
                        'porcentaje_entregas_fuera_plazo' => $data['porcentaje_entregas_fuera_plazo'] ?? 0,
                        'porcentaje_devoluciones' => $data['porcentaje_devoluciones'] ?? 0,
                        'porcentaje_reclamos' => $data['porcentaje_reclamos'] ?? 0,
                        'cantidad_incidentes' => $data['cantidad_incidentes'] ?? 0,
                        'saldo_por_pagar' => $data['saldo_por_pagar'] ?? 0,
                        'promedio_mensual' => $data['promedio_mensual'] ?? 0,
                        'productos_frecuentes' => $data['productos_frecuentes'] ?? [],
                    ]);

                    $datosProveedor->contables()->create([
                        'cuenta_por_pagar' => $data['cuenta_por_pagar'] ?? null,
                        'cuenta_gasto_predeterminada' => $data['cuenta_gasto_predeterminada'] ?? null,
                        'cuenta_inventario_predeterminada' => $data['cuenta_inventario_predeterminada'] ?? null,
                        'cuenta_anticipo' => $data['cuenta_anticipo'] ?? null,
                        'centro_costo' => $data['centro_costo'] ?? null,
                        'proyecto' => $data['proyecto'] ?? null,
                    ]);
                }
            });

            return response()->json(['message' => 'Proveedor registrado correctamente.']);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Ocurrió un error al registrar el proveedor.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => [
                'required',
                'string',
                Rule::unique('personas')
                    ->ignore($persona->id)
                    ->where(
                        fn($query) =>
                        $query->where('id_establecimiento', session('establecimiento_id'))
                            ->whereJsonContains('tipo', 'proveedor')
                    ),
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:10',
            'email' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'tipo_empresa' => 'nullable|string',
            'nombre_comercial' => 'nullable|string',
            'id_banco' => 'nullable|string',
            'tipo_cuenta' => 'nullable|string',
            'numero_cuenta' => 'nullable|string',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:50',

            // datos proveedor
            'codigo_interno' => 'nullable|string',
            'categoria_proveedor' => 'nullable|string',
            'segmento' => 'nullable|string',
            'comprador_asignado' => 'nullable|string',
            'zona' => 'nullable|string',
            'clasificacion' => 'nullable|string',
            'inicio_relacion' => 'nullable|date_format:d/m/Y',
            'estado' => 'required|in:activo,inactivo',
            'configuracion_especial' => 'nullable|array',

            // configuración
            'notas' => 'nullable|string',

            // financieros
            'limite_credito' => 'nullable|numeric',
            'dias_credito' => 'nullable|integer',
            'forma_pago' => 'nullable|string',
            'observaciones_crediticias' => 'nullable|string',
            'historial_pagos' => 'nullable|array',
            'nivel_riesgo' => 'nullable|string',

            // tributarios
            'agente_retencion' => 'nullable|boolean',
            'contribuyente_especial' => 'nullable|boolean',
            'obligado_contabilidad' => 'nullable|boolean',
            'parte_relacionada' => 'nullable|boolean',
            'regimen_tributario' => 'nullable|string',
            'codigo_tipo_proveedor_sri' => 'nullable|string',
            'retencion_fuente' => 'nullable|array',
            'retencion_iva' => 'nullable|array',

            // kpi
            'total_compras_anual' => 'nullable|numeric',
            'cantidad_facturas' => 'nullable|integer',
            'monto_promedio_compra' => 'nullable|numeric',
            'ultima_compra_fecha' => 'nullable|date_format:d/m/Y',
            'ultima_compra_monto' => 'nullable|numeric',
            'dias_promedio_pago' => 'nullable|integer',
            'porcentaje_entregas_a_tiempo' => 'nullable|numeric',
            'porcentaje_entregas_fuera_plazo' => 'nullable|numeric',
            'porcentaje_devoluciones' => 'nullable|numeric',
            'porcentaje_reclamos' => 'nullable|numeric',
            'cantidad_incidentes' => 'nullable|integer',
            'saldo_por_pagar' => 'nullable|numeric',
            'promedio_mensual' => 'nullable|numeric',
            'productos_frecuentes' => 'nullable|array',

            // documentos
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,zip|max:5120',
            'tipos.*' => 'nullable|string',

            // contables
            'cuenta_por_pagar' => 'nullable|string|max:20',
            'cuenta_gasto_predeterminada' => 'nullable|string|max:20',
            'cuenta_inventario_predeterminada' => 'nullable|string|max:20',
            'cuenta_anticipo' => 'nullable|string|max:20',
            'centro_costo' => 'nullable|string|max:20',
            'proyecto' => 'nullable|string|max:20'
        ]);

        $validator->after(function ($v) use ($request) {
            $emails = array_map('trim', explode(',', $request->input('email')));
            foreach ($emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $v->errors()->add('email', "El correo \"$email\" no es válido.");
                }
            }
        });

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        if (!empty($data['inicio_relacion'])) {
            $data['inicio_relacion'] = Carbon::createFromFormat('d/m/Y', $data['inicio_relacion'])->format('Y-m-d');
        }

        if (!empty($data['ultima_compra_fecha'])) {
            $data['ultima_compra_fecha'] = Carbon::createFromFormat('d/m/Y', $data['ultima_compra_fecha'])->format('Y-m-d');
        }

        foreach (['nombre', 'direccion', 'provincia', 'ciudad', 'pais', 'nombre_comercial'] as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
            }
        }

        $data['email'] = !empty($data['email'])
            ? implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))))
            : null;

        DB::transaction(function () use ($persona, $data, $request) {
            $tipos = $persona->tipo ?? [];
            if (!in_array('proveedor', $tipos)) {
                $tipos[] = 'proveedor';
            }
            $persona->tipo = $tipos;
            $persona->fill($data)->save();

            $datosProveedor = $persona->datosProveedor;
            if (!$datosProveedor) {
                $datosProveedor = $persona->datosProveedor()->create([
                    'codigo_interno' => $data['codigo_interno'] ?? null,
                    'categoria_proveedor' => $data['categoria_proveedor'] ?? null,
                    'segmento' => $data['segmento'] ?? null,
                    'fecha_registro' => Carbon::now(),
                    'comprador_asignado' => $data['comprador_asignado'] ?? null,
                    'zona' => $data['zona'] ?? null,
                    'provincia' => $data['provincia'] ?? null,
                    'ciudad' => $data['ciudad'] ?? null,
                    'pais' => $data['pais'] ?? null,
                    'id_banco ' => $data['id_banco '] ?? null,
                    'tipo_cuenta' => $data['tipo_cuenta'] ?? null,
                    'numero_cuenta' => $data['numero_cuenta'] ?? null,
                    'clasificacion' => $data['clasificacion'] ?? null,
                    'inicio_relacion' => $data['inicio_relacion'] ?? null,
                    'estado' => $data['estado'],
                    'configuracion_especial' => $data['configuracion_especial'] ?? null,
                ]);
            } else {
                $datosProveedor->update([
                    'codigo_interno' => $data['codigo_interno'] ?? null,
                    'categoria_proveedor' => $data['categoria_proveedor'] ?? null,
                    'segmento' => $data['segmento'] ?? null,
                    'fecha_registro' => Carbon::now(),
                    'comprador_asignado' => $data['comprador_asignado'] ?? null,
                    'zona' => $data['zona'] ?? null,
                    'provincia' => $data['provincia'] ?? null,
                    'ciudad' => $data['ciudad'] ?? null,
                    'pais' => $data['pais'] ?? null,
                    'id_banco ' => $data['id_banco '] ?? null,
                    'tipo_cuenta' => $data['tipo_cuenta'] ?? null,
                    'numero_cuenta' => $data['numero_cuenta'] ?? null,
                    'clasificacion' => $data['clasificacion'] ?? null,
                    'inicio_relacion' => $data['inicio_relacion'] ?? null,
                    'estado' => $data['estado'],
                    'configuracion_especial' => $data['configuracion_especial'] ?? null,
                ]);
            }

            $datosProveedor->configuracion()->updateOrCreate([], [
                'notas' => $data['notas'] ?? null
            ]);

            $datosProveedor->financieros()->updateOrCreate([], [
                'limite_credito' => $data['limite_credito'] ?? null,
                'dias_credito' => $data['dias_credito'] ?? null,
                'forma_pago' => $data['forma_pago'] ?? null,
                'observaciones_crediticias' => $data['observaciones_crediticias'] ?? null,
                'historial_pagos' => !empty($data['historial_pagos']) ? $data['historial_pagos'] : null,
                'nivel_riesgo' => $data['nivel_riesgo'] ?? null,
            ]);

            $datosProveedor->tributarios()->updateOrCreate([], [
                'agente_retencion' => $data['agente_retencion'] ?? false,
                'contribuyente_especial' => $data['contribuyente_especial'] ?? false,
                'obligado_contabilidad' => $data['obligado_contabilidad'] ?? false,
                'parte_relacionada' => $data['parte_relacionada'] ?? false,
                'regimen_tributario' => $data['regimen_tributario'] ?? null,
                'codigo_tipo_proveedor_sri' => $data['codigo_tipo_proveedor_sri'] ?? null,
                'retencion_fuente' => $data['retencion_fuente'] ?? null,
                'retencion_iva' => $data['retencion_iva'] ?? null,
            ]);

            $datosProveedor->kpi()->updateOrCreate([], [
                'total_compras_anual' => $data['total_compras_anual'] ?? 0,
                'cantidad_facturas' => $data['cantidad_facturas'] ?? 0,
                'monto_promedio_compra' => $data['monto_promedio_compra'] ?? 0,
                'ultima_compra_fecha' => $data['ultima_compra_fecha'] ?? null,
                'ultima_compra_monto' => $data['ultima_compra_monto'] ?? 0,
                'dias_promedio_pago' => $data['dias_promedio_pago'] ?? 0,
                'porcentaje_entregas_a_tiempo' => $data['porcentaje_entregas_a_tiempo'] ?? 0,
                'porcentaje_entregas_fuera_plazo' => $data['porcentaje_entregas_fuera_plazo'] ?? 0,
                'porcentaje_devoluciones' => $data['porcentaje_devoluciones'] ?? 0,
                'porcentaje_reclamos' => $data['porcentaje_reclamos'] ?? 0,
                'cantidad_incidentes' => $data['cantidad_incidentes'] ?? 0,
                'saldo_por_pagar' => $data['saldo_por_pagar'] ?? 0,
                'promedio_mensual' => $data['promedio_mensual'] ?? 0,
                'productos_frecuentes' => !empty($data['productos_frecuentes']) ? $data['productos_frecuentes'] : [],
            ]);

            $datosProveedor->contables()->updateOrCreate([], [
                'cuenta_por_pagar' => $data['cuenta_por_pagar'] ?? null,
                'cuenta_gasto_predeterminada' => $data['cuenta_gasto_predeterminada'] ?? null,
                'cuenta_inventario_predeterminada' => $data['cuenta_inventario_predeterminada'] ?? null,
                'cuenta_anticipo' => $data['cuenta_anticipo'] ?? null,
                'centro_costo' => $data['centro_costo'] ?? null,
                'proyecto' => $data['proyecto'] ?? null,
            ]);

            if ($request->hasFile('documentos')) {
                $tipos = $request->input('tipos', []);
                foreach ($request->file('documentos') as $index => $archivo) {
                    $tipo = $tipos[$index] ?? null;

                    if ($tipo) {
                        $documentoExistente = $datosProveedor->documentos()->where('tipo', $tipo)->first();
                        if ($documentoExistente) {
                            Storage::disk('public')->delete($documentoExistente->archivo);
                            $documentoExistente->delete();
                        }
                    }

                    $ruta = $archivo->store("proveedores/documentos", 'public');
                    $datosProveedor->documentos()->create([
                        'tipo' => $tipo,
                        'archivo' => $ruta
                    ]);
                }
            }

            $datosProveedor->historial()->create([
                'descripcion' => 'Proveedor actualizado por el usuario ID: ' . Auth::id(),
                'tipo' => 'actualizacion',
                'fecha' => now(),
            ]);
        });

        return response()->json(['message' => 'Proveedor actualizado correctamente.']);
    }



    public function edit(Persona $proveedor)
    {
        $proveedor->load([
            'datosProveedor',
            'datosProveedor.financieros',
            'datosProveedor.tributarios',
            'datosProveedor.configuracion',
            'datosProveedor.kpi',
            'datosProveedor.documentos',
            'datosProveedor.contables',
        ]);

        $proveedor->datosProveedor?->append('inicio_relacion_formatted');

        return response()->json($proveedor);
    }

    //para eliminar los documentos cargados en proveedores
    public function eliminarDocumento(DocumentoProveedor $documento)
    {
        try {
            if (Storage::disk('public')->exists($documento->archivo)) {
                Storage::disk('public')->delete($documento->archivo);
            }

            $documento->delete();

            return response()->json(['message' => 'Documento eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el documento.'], 500);
        }
    }
}
