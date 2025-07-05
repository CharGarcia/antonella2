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


class VendedorController extends Controller
{
    public function index()
    {
        return view('empresa.vendedores.index');
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

        // Incluye la relación con datosVendedor para filtrar y mostrar estado
        $vendedores = Persona::where('id_establecimiento', $establecimientoId)
            ->whereJsonContains('tipo', ['vendedor'])
            ->with('datosVendedor');

        return DataTables::eloquent($vendedores)
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
                                    $query->whereHas('datosVendedor', function ($q) use ($estado) {
                                        $q->where('estado', $estado);
                                    });
                                }
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($vendedor) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-vendedor" data-id="' . $vendedor->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-vendedor" data-id="' . $vendedor->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado', function ($vendedor) {
                $estado = $vendedor->datosVendedor->estado ?? 'activo';
                return $estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }


    public function destroy($id)
    {
        $vendedor = Persona::findOrFail($id);

        if (!in_array('vendedor', $vendedor->tipo ?? [])) {
            return response()->json(['message' => 'No es un vendedor válido.'], 400);
        }

        $tipos = array_filter($vendedor->tipo, fn($tipo) => $tipo !== 'vendedor');

        if (!empty($tipos)) {
            $vendedor->update(['tipo' => array_values($tipos)]);
        } else {
            $vendedor->delete();
        }

        return response()->json(['message' => 'Vendedor eliminado']);
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
            'nombre.required' => 'El nombre del vendedor es obligatorio.',
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

            // datos_vendedor
            'codigo_interno' => 'nullable|string',
            'perfil' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'vendedor_asignado' => 'nullable|string',
            'zona' => 'nullable|string',
            'inicio_relacion' => 'nullable|date',
            'estado' => 'required|in:activo,inactivo',
            'informacion_adicional' => 'nullable|string',
            'monto_ventas_asignado'  => 'nullable|numeric|min:0',
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
                    'tipo' => ['vendedor']
                ]
            ));

            $vendedor = $persona->datosVendedor()->create([
                'codigo_interno' => $data['codigo_interno'] ?? null,
                'perfil' => $data['perfil'] ?? null,
                'fecha_registro' => $data['fecha_registro'] ?? null,
                'zona' => $data['zona'] ?? null,
                'inicio_relacion' => $data['inicio_relacion'] ?? null,
                'estado' => $data['estado'],
                'informacion_adicional' => $data['informacion_adicional'] ?? null,
                'monto_ventas_asignado'  => $data['monto_ventas_asignado']  ?? 0,
            ]);
        });

        return response()->json(['message' => 'Vendedor registrado correctamente.']);
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
                        ->whereJsonContains('tipo', 'vendedor')
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

            // datos_vendedor
            'codigo_interno' => 'nullable|string',
            'perfil' => 'nullable|string',
            'fecha_registro' => 'nullable|date',
            'zona' => 'nullable|string',
            'inicio_relacion' => 'nullable|date',
            'informacion_adicional' => 'nullable|string',
            'monto_ventas_asignado'  => 'nullable|numeric|min:0',
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
        if (!in_array('vendedor', $tipos)) {
            $tipos[] = 'vendedor';
        }
        $persona->tipo = $tipos;
        $persona->fill($data)->save();

        $datosVendedor = $persona->datosVendedor;
        if ($datosVendedor) {
            $datosVendedor->update([
                'codigo_interno' => $data['codigo_interno'] ?? null,
                'perfil' => $data['perfil'] ?? null,
                'fecha_registro' => $data['fecha_registro'] ?? null,
                'zona' => $data['zona'] ?? null,
                'inicio_relacion' => $data['inicio_relacion'] ?? null,
                'estado' => $data['estado'],
                'informacion_adicional' => $data['informacion_adicional'] ?? null,
                'monto_ventas_asignado'  => $data['monto_ventas_asignado'] ?? 0,
            ]);
        }

        return response()->json(['message' => 'Vendedor actualizado correctamente.']);
    }

    public function edit(Persona $vendedor)
    {
        $vendedor->load([
            'datosVendedor',
        ]);

        return response()->json($vendedor);
    }
}
