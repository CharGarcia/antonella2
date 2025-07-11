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
use App\Models\Persona;
use App\Models\DocumentoCliente;
use App\Models\ListaPrecio;
use App\Models\FormasPagoSri;


class ClienteController extends Controller
{
    public function index()
    {
        $vendedores = Persona::query()
            ->where('id_establecimiento', session('establecimiento_id'))
            ->whereJsonContains('tipo', ['vendedor'])
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        $listasPrecios = ListaPrecio::where('id_establecimiento', session('establecimiento_id'))
            ->where('estado', true)
            ->orderBy('nombre')
            ->pluck('nombre', 'id');

        $formasPago = FormasPagoSri::pluck('descripcion', 'codigo'); // O el campo adecuado

        return view('empresa.clientes.index', compact('formasPago', 'vendedores', 'listasPrecios'));
    }

    public function getData(Request $request)
    {
        $user = Auth::user();
        $establecimientoId = session('establecimiento_id');
        $submenuId = session('submenu_id');

        $permisos = \App\Models\SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();

        // Incluye la relación con datosCliente para filtrar y mostrar estado
        $clientes = Persona::where('id_establecimiento', $establecimientoId)
            ->whereJsonContains('tipo', ['cliente'])
            ->with('datosCliente');

        return DataTables::eloquent($clientes)
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
                                    $query->whereHas('datosCliente', function ($q) use ($estado) {
                                        $q->where('estado', $estado);
                                    });
                                }
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($cliente) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-cliente" data-id="' . $cliente->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-cliente" data-id="' . $cliente->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado', function ($cliente) {
                $estado = $cliente->datosCliente->estado ?? 'activo';
                return $estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }


    public function destroy($id)
    {
        $cliente = Persona::findOrFail($id);

        if (!in_array('cliente', $cliente->tipo ?? [])) {
            return response()->json(['message' => 'No es un cliente válido.'], 400);
        }

        $tipos = array_filter($cliente->tipo, fn($tipo) => $tipo !== 'cliente');

        if (!empty($tipos)) {
            $cliente->update(['tipo' => array_values($tipos)]);
        } else {
            $cliente->delete();
        }

        return response()->json(['message' => 'Cliente eliminado']);
    }

    public function buscarPorIdentificacion(Request $request)
    {
        $numero = trim($request->numero_identificacion);

        $persona = Persona::where('numero_identificacion', $numero)->first();

        return response()->json([
            'encontrado' => (bool) $persona,
            'persona' => $persona
        ]);
    }

    public function store(Request $request)
    {
        $messages = [
            'tipo_identificacion.required' => 'El tipo de identificación es obligatorio.',
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'email.required' => 'El campo email es obligatorio.',
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
            'tipo_cuenta' => 'nullable|string',
            'numero_cuenta' => 'nullable|string',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:50',

            // datos_cliente
            'codigo_interno' => 'nullable|string',
            'categoria_cliente' => 'nullable|string',
            'segmento' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'vendedor_asignado' => 'nullable|string',
            'lista_precios' => 'nullable|string',
            'canal_venta' => 'nullable|string',
            'zona' => 'nullable|string',
            'clasificacion' => 'nullable|string',
            'inicio_relacion' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
            'configuracion_especial' => 'nullable|array',

            // configuracion
            'notas' => 'nullable|string',
            'permitir_venta_con_deuda' => 'nullable|boolean',
            'aplica_descuento' => 'nullable|boolean',

            // financieros
            'cupo_credito' => 'nullable|numeric',
            'dias_credito' => 'nullable|integer',
            'forma_pago' => 'nullable|string',
            'observaciones_crediticias' => 'nullable|string',
            'historial_pagos' => 'nullable|array',
            'nivel_riesgo' => 'nullable|string',

            // tributarios
            'agente_retencion' => 'nullable|boolean',
            'contribuyente_especial' => 'nullable|boolean',
            'obligado_contabilidad' => 'nullable|boolean',
            'regimen_tributario' => 'nullable|string',
            'retencion_fuente' => 'nullable|string',
            'retencion_iva' => 'nullable|string',
            'porcentajes_retencion' => 'nullable|array',

            // kpi
            'total_ventas' => 'nullable|numeric',
            'ultima_compra_fecha' => 'nullable|date',
            'ultima_compra_monto' => 'nullable|numeric',
            'dias_promedio_pago' => 'nullable|integer',
            'saldo_por_cobrar' => 'nullable|numeric',
            'promedio_mensual' => 'nullable|numeric',
            'productos_frecuentes' => 'nullable|array',

            // documentos
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,zip|max:5120',
            'tipos.*' => 'nullable|string'

        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        DB::transaction(function () use ($data, $request) {
            $data['email'] = implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))));

            foreach (['nombre', 'direccion', 'provincia', 'ciudad', 'pais', 'nombre_comercial'] as $campo) {
                if (!empty($data[$campo])) {
                    $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
                }
            }

            $persona = Persona::create(array_merge(
                $data,
                [
                    'id_user' => Auth::id(),
                    'id_establecimiento' => session('establecimiento_id'),
                    'tipo' => ['cliente']
                ]
            ));

            $cliente = $persona->datosCliente()->create([
                'codigo_interno' => $data['codigo_interno'] ?? null,
                'categoria_cliente' => $data['categoria_cliente'] ?? null,
                'segmento' => $data['segmento'] ?? null,
                'fecha_registro' => $data['fecha_registro'] ?? null,
                'vendedor_asignado' => $data['vendedor_asignado'] ?? null,
                'lista_precios' => $data['lista_precios'] ?? null,
                'canal_venta' => $data['canal_venta'] ?? null,
                'zona' => $data['zona'] ?? null,
                'clasificacion' => $data['clasificacion'] ?? null,
                'inicio_relacion' => $data['inicio_relacion'] ?? null,
                'estado' => $data['estado'],
                'configuracion_especial' => $data['configuracion_especial'] ?? null,
            ]);

            $cliente->configuracion()->create([
                'notas' => $data['notas'] ?? null,
                'permitir_venta_con_deuda' => $data['permitir_venta_con_deuda'] ?? true,
                'aplica_descuento' => $data['aplica_descuento'] ?? false,
            ]);

            $cliente->financieros()->create([
                'cupo_credito' => $data['cupo_credito'] ?? null,
                'dias_credito' => $data['dias_credito'] ?? null,
                'forma_pago' => $data['forma_pago'] ?? null,
                'observaciones_crediticias' => $data['observaciones_crediticias'] ?? null,
                'historial_pagos' => $data['historial_pagos'] ?? null,
                'nivel_riesgo' => $data['nivel_riesgo'] ?? null,
            ]);

            $cliente->tributarios()->create([
                'agente_retencion' => $data['agente_retencion'] ?? false,
                'contribuyente_especial' => $data['contribuyente_especial'] ?? false,
                'obligado_contabilidad' => $data['obligado_contabilidad'] ?? false,
                'regimen_tributario' => $data['regimen_tributario'] ?? null,
                'retencion_fuente' => $data['retencion_fuente'] ?? null,
                'retencion_iva' => $data['retencion_iva'] ?? null,
                'porcentajes_retencion' => $data['porcentajes_retencion'] ?? null,
            ]);

            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $index => $archivo) {
                    $tipo = $request->input('tipos')[$index] ?? null;
                    $ruta = $archivo->store("clientes/documentos", 'public');
                    $cliente->documentos()->create([
                        'tipo' => $tipo,
                        'archivo' => $ruta
                    ]);
                }
            }

            $cliente->historial()->create([
                'descripcion' => 'Cliente creado por el usuario ID: ' . Auth::id(),
                'tipo' => 'creacion'
            ]);

            $cliente->kpi()->create([
                'total_ventas' => $data['total_ventas'] ?? 0,
                'ultima_compra_fecha' => $data['ultima_compra_fecha'] ?? null,
                'ultima_compra_monto' => $data['ultima_compra_monto'] ?? 0,
                'dias_promedio_pago' => $data['dias_promedio_pago'] ?? 0,
                'saldo_por_cobrar' => $data['saldo_por_cobrar'] ?? 0,
                'promedio_mensual' => $data['promedio_mensual'] ?? 0,
                'productos_frecuentes' => $data['productos_frecuentes'] ?? [],
            ]);
        });

        return response()->json(['message' => 'Cliente registrado correctamente.']);
    }


    public function update(Request $request, $id)
    {
        $persona = Persona::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => [
                'required',
                'string',
                Rule::unique('personas')->ignore($persona->id)->where(
                    fn($query) => $query->where('id_establecimiento', session('establecimiento_id'))
                        ->whereJsonContains('tipo', 'cliente')
                ),
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'tipo_empresa' => 'nullable|string',
            'nombre_comercial' => 'nullable|string',
            'tipo_cuenta' => 'nullable|string',
            'numero_cuenta' => 'nullable|string',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'pais' => 'nullable|string|max:50',
            'estado' => 'required|in:activo,inactivo',

            // datos_cliente
            'codigo_interno' => 'nullable|string',
            'categoria_cliente' => 'nullable|string',
            'segmento' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'vendedor_asignado' => 'nullable|string',
            'lista_precios' => 'nullable|string',
            'canal_venta' => 'nullable|string',
            'zona' => 'nullable|string',
            'clasificacion' => 'nullable|string',
            'inicio_relacion' => 'nullable|date',
            'configuracion_especial' => 'nullable|array',

            // configuración
            'notas' => 'nullable|string',
            'permitir_venta_con_deuda' => 'nullable|boolean',
            'aplica_descuento' => 'nullable|boolean',

            // financieros
            'cupo_credito' => 'nullable|numeric',
            'dias_credito' => 'nullable|integer',
            'forma_pago' => 'nullable|string',
            'observaciones_crediticias' => 'nullable|string',
            'historial_pagos' => 'nullable|array',
            'nivel_riesgo' => 'nullable|string',

            // tributarios
            'agente_retencion' => 'nullable|boolean',
            'contribuyente_especial' => 'nullable|boolean',
            'obligado_contabilidad' => 'nullable|boolean',
            'regimen_tributario' => 'nullable|string',
            'retencion_fuente' => 'nullable|string',
            'retencion_iva' => 'nullable|string',
            'porcentajes_retencion' => 'nullable|array',

            // kpi
            'total_ventas' => 'nullable|numeric',
            'ultima_compra_fecha' => 'nullable|date',
            'ultima_compra_monto' => 'nullable|numeric',
            'dias_promedio_pago' => 'nullable|integer',
            'saldo_por_cobrar' => 'nullable|numeric',
            'promedio_mensual' => 'nullable|numeric',
            'productos_frecuentes' => 'nullable|array',

            // documentos
            'documentos.*' => 'file|mimes:pdf,jpg,jpeg,png,doc,docx,zip|max:5120',
            'tipos.*' => 'nullable|string'
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

        foreach (['nombre', 'direccion', 'provincia', 'ciudad', 'pais', 'nombre_comercial'] as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
            }
        }

        $data['email'] = implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))));

        $tipos = $persona->tipo ?? [];
        if (!in_array('cliente', $tipos)) {
            $tipos[] = 'cliente';
        }
        $persona->tipo = $tipos;
        $persona->fill($data)->save();

        $datosCliente = $persona->datosCliente;
        if ($datosCliente) {
            $datosCliente->update([
                'codigo_interno' => $data['codigo_interno'] ?? null,
                'categoria_cliente' => $data['categoria_cliente'] ?? null,
                'segmento' => $data['segmento'] ?? null,
                'fecha_registro' => $data['fecha_registro'] ?? null,
                'vendedor_asignado' => $data['vendedor_asignado'] ?? null,
                'lista_precios' => $data['lista_precios'] ?? null,
                'canal_venta' => $data['canal_venta'] ?? null,
                'zona' => $data['zona'] ?? null,
                'clasificacion' => $data['clasificacion'] ?? null,
                'inicio_relacion' => $data['inicio_relacion'] ?? null,
                'estado' => $data['estado'],
                'configuracion_especial' => $data['configuracion_especial'] ?? null,
            ]);

            // Configuración
            $datosCliente->configuracion()->updateOrCreate([], $request->only([
                'notas',
                'permitir_venta_con_deuda',
                'aplica_descuento'
            ]));

            // Financieros
            $datosCliente->financieros()->updateOrCreate([], $request->only([
                'cupo_credito',
                'dias_credito',
                'forma_pago',
                'observaciones_crediticias',
                'historial_pagos',
                'nivel_riesgo'
            ]));

            // Tributarios
            $datosCliente->tributarios()->updateOrCreate([], $request->only([
                'agente_retencion',
                'contribuyente_especial',
                'obligado_contabilidad',
                'regimen_tributario',
                'retencion_fuente',
                'retencion_iva',
                'porcentajes_retencion'
            ]));

            // KPI
            $datosCliente->kpi()->updateOrCreate([], $request->only([
                'total_ventas',
                'ultima_compra_fecha',
                'ultima_compra_monto',
                'dias_promedio_pago',
                'saldo_por_cobrar',
                'promedio_mensual',
                'productos_frecuentes'
            ]));

            // Documentos con control de versiones
            if ($request->hasFile('documentos')) {
                foreach ($request->file('documentos') as $index => $archivo) {
                    $tipo = $request->input('tipos')[$index] ?? null;

                    if ($tipo) {
                        $documentoExistente = $datosCliente->documentos()->where('tipo', $tipo)->first();
                        if ($documentoExistente) {
                            Storage::disk('public')->delete($documentoExistente->archivo);
                            $documentoExistente->delete();
                        }
                    }

                    $ruta = $archivo->store("clientes/documentos", 'public');
                    $datosCliente->documentos()->create([
                        'tipo' => $tipo,
                        'archivo' => $ruta
                    ]);
                }
            }

            // Historial
            $datosCliente->historial()->create([
                'descripcion' => 'Cliente actualizado por el usuario ID: ' . Auth::id(),
                'tipo' => 'actualizacion'
            ]);
        }

        return response()->json(['message' => 'Cliente actualizado correctamente.']);
    }

    public function edit(Persona $cliente)
    {
        $cliente->load([
            'datosCliente',
            'datosCliente.financieros',
            'datosCliente.tributarios',
            'datosCliente.configuracion',
            'datosCliente.kpi',
            'datosCliente.documentos'
        ]);

        return response()->json($cliente);
    }

    //para eliminar los documentos cargados en clientes
    public function eliminarDocumento(DocumentoCliente $documento)
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
