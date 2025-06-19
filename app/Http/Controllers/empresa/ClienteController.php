<?php

namespace App\Http\Controllers\empresa;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;



class ClienteController extends Controller
{
    public function index()
    {
        return view('empresa.clientes.index'); //, compact('permisos')
    }

    public function getData(Request $request)
    {

        $user = Auth::user();
        $establecimientoId = session('establecimiento_id');
        // Obtener permisos del usuario para esta ruta
        $submenuId = session('submenu_id');

        $permisos = \App\Models\SubmenuEstablecimientoUsuario::where('user_id', $user->id)
            ->where('establecimiento_id', $establecimientoId)
            ->where('submenu_id', $submenuId)
            ->first();


        // Query principal
        $cliente = Persona::with('vendedor')
            ->where('id_establecimiento', $establecimientoId)
            ->whereJsonContains('tipo', ['cliente']);

        return DataTables::eloquent($cliente)
            ->filter(function ($cliente) use ($request) {
                foreach ($request->columns as $index => $column) {
                    $searchValue = $column['search']['value'] ?? '';
                    if ($searchValue !== '') {
                        switch ($index) {
                            case 0:
                                $cliente->where('nombre', 'like', "%$searchValue%");
                                break;
                            case 1:
                                $cliente->where('numero_identificacion', 'like', "%$searchValue%");
                                break;
                            case 2:
                                $cliente->where('telefono', 'like', "%$searchValue%");
                                break;
                            case 3:
                                $cliente->where('email', 'like', "%$searchValue%");
                                break;
                            case 4:
                                $cliente->where('direccion', 'like', "%$searchValue%");
                                break;
                            case 5:
                                $cliente->whereHas('vendedor', function ($q) use ($searchValue) {
                                    $q->where('nombre', 'like', "%$searchValue%");
                                });
                                break;
                            case 6:
                                $estado = $searchValue == '1';
                                $cliente->where('estado', $estado);
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($cliente) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';

                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-cliente" data-id="' . $cliente->id . '" title="Editar">
            <i class="fas fa-edit"></i>
        </button>';
                }

                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-cliente" data-id="' . $cliente->id . '" title="Eliminar">
            <i class="fas fa-trash-alt"></i>
        </button>';
                }

                $botones .= '</div>';

                return $botones;
            })

            ->addColumn('vendedor_nombre', function ($cliente) {
                return optional($cliente->vendedor)->nombre ?? '-';
            })
            ->editColumn('estado', function ($cliente) {
                return $cliente->estado == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }



    public function store(Request $request)
    {
        $messages = [
            'tipo_identificacion.required' => 'El tipo de identificación es obligatorio.',
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'numero_identificacion.unique' => 'Ya existe un cliente con este número de identificación en este establecimiento.',
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El campo email debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',
            'telefono.max' => 'El teléfono no puede superar los 10 caracteres.',
            'provincia.max' => 'La provincia no puede superar los 50 caracteres.',
            'ciudad.max' => 'La ciudad no puede superar los 50 caracteres.',
            'plazo_credito.integer' => 'El plazo de crédito debe ser un número entero.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser activo o inactivo.'
        ];

        $data = $request->validate([
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => [
                'required',
                'string',
                Rule::unique('personas')->where(function ($query) {
                    return $query->where('id_establecimiento', session('establecimiento_id'))
                        ->whereJsonContains('tipo', 'cliente');
                }),
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:10',
            'email' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail("El correo \"$email\" no es válido.");
                        }
                    }
                },
            ],
            'direccion' => 'nullable|string|max:255',
            'id_vendedor' => 'nullable|exists:users,id',
            'plazo_credito' => 'nullable|integer|min:0',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'estado' => 'required|boolean',
        ], $messages);

        // Validación condicional para tipo_identificación
        if ($data['tipo_identificacion'] === '04' && strlen($data['numero_identificacion']) !== 13) {
            return response()->json([
                'errors' => [
                    'numero_identificacion' => ['El número de RUC debe tener exactamente 13 dígitos.']
                ]
            ], 422);
        }

        if ($data['tipo_identificacion'] === '05' && strlen($data['numero_identificacion']) !== 10) {
            return response()->json([
                'errors' => [
                    'numero_identificacion' => ['El número de cédula debe tener exactamente 10 dígitos.']
                ]
            ], 422);
        }

        // Limpiar y convertir a mayúsculas los campos de texto
        $camposTexto = ['nombre', 'direccion', 'provincia', 'ciudad'];
        foreach ($camposTexto as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
            }
        }
        // Normalizar correos
        $data['email'] = implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))));

        $persona = Persona::where('numero_identificacion', $data['numero_identificacion'])->first();

        if ($persona && $persona->id_establecimiento !== session('establecimiento_id')) {
            $persona = null;
        }

        if ($persona) {
            $tipos = $persona->tipo ?? [];
            if (!in_array('cliente', $tipos)) {
                $tipos[] = 'cliente';
            }
            $persona->update(array_merge($data, ['tipo' => $tipos]));
        } else {
            $data['id_user'] = Auth::id();
            $data['id_establecimiento'] = session('establecimiento_id');
            $data['tipo'] = ['cliente'];
            Persona::create($data);
        }

        return response()->json(['message' => 'Cliente creado']);
    }


    public function update(Request $request, $id)
    {
        $cliente = Persona::findOrFail($id);
        $messages = [
            'tipo_identificacion.required' => 'El tipo de identificación es obligatorio.',
            'numero_identificacion.required' => 'El número de identificación es obligatorio.',
            'numero_identificacion.unique' => 'Ya existe un cliente con este número de identificación en este establecimiento.',
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El campo email debe ser una cadena de texto.',
            'direccion.max' => 'La dirección no puede superar los 255 caracteres.',
            'telefono.max' => 'El teléfono no puede superar los 10 caracteres.',
            'provincia.max' => 'La provincia no puede superar los 50 caracteres.',
            'ciudad.max' => 'La ciudad no puede superar los 50 caracteres.',
            'plazo_credito.integer' => 'El plazo de crédito debe ser un número entero.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.boolean' => 'El estado debe ser activo o inactivo.'
        ];

        $data = $request->validate([
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => [
                'required',
                'string',
                Rule::unique('personas')->ignore($cliente->id)->where(function ($query) {
                    return $query->where('id_establecimiento', session('establecimiento_id'))
                        ->whereJsonContains('tipo', 'cliente');
                }),
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $emails = array_map('trim', explode(',', $value));
                    foreach ($emails as $email) {
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            $fail("El correo \"$email\" no es válido.");
                        }
                    }
                },
            ],
            'direccion' => 'nullable|string|max:255',
            'id_vendedor' => 'nullable|exists:users,id',
            'plazo_credito' => 'nullable|integer|min:0',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'estado' => 'required|boolean',
        ], $messages);

        // Validación condicional para tipo_identificación
        if ($data['tipo_identificacion'] === '04' && strlen($data['numero_identificacion']) !== 13) {
            return response()->json([
                'errors' => [
                    'numero_identificacion' => ['El número de RUC debe tener exactamente 13 dígitos.']
                ]
            ], 422);
        }

        if ($data['tipo_identificacion'] === '05' && strlen($data['numero_identificacion']) !== 10) {
            return response()->json([
                'errors' => [
                    'numero_identificacion' => ['El número de cédula debe tener exactamente 10 dígitos.']
                ]
            ], 422);
        }

        // Limpiar y convertir a mayúsculas los campos de texto
        $camposTexto = ['nombre', 'direccion', 'provincia', 'ciudad'];
        foreach ($camposTexto as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = strtoupper(preg_replace('/\s+/', ' ', trim($data[$campo])));
            }
        }

        // Normalizar correos
        $data['email'] = implode(',', array_map('strtolower', array_map('trim', explode(',', $data['email']))));

        $tipos = $cliente->tipo ?? [];
        if (!in_array('cliente', $tipos)) {
            $tipos[] = 'cliente';
        }
        $data['tipo'] = $tipos;

        $cliente->update($data);

        return response()->json(['message' => 'Cliente actualizado']);
    }


    //para que me cargue los datos para editar
    public function edit(Persona $cliente)
    {
        return response()->json($cliente);
    }


    public function destroy($id)
    {
        $cliente = Persona::findOrFail($id);

        // Verifica que sea tipo cliente
        if (!in_array('cliente', $cliente->tipo ?? [])) {
            return response()->json(['message' => 'No es un cliente válido.'], 400);
        }

        // Aquí podrías validar si tiene facturas, pedidos, etc.
        // if ($cliente->facturas()->exists()) {
        //     return response()->json(['message' => 'El cliente tiene facturas asociadas y no se puede eliminar.'], 400);
        // }

        // Remueve el tipo cliente
        $tipos = array_filter($cliente->tipo, fn($tipo) => $tipo !== 'cliente');

        if (!empty($tipos)) {
            $cliente->update(['tipo' => array_values($tipos)]);
        } else {
            $cliente->delete();
        }

        return response()->json(['message' => 'Cliente eliminado']);
    }

    //para buscar informacion en la tabla personas
    public function buscarPorIdentificacion(Request $request)
    {
        $numero = trim($request->numero_identificacion);

        $persona = Persona::where('numero_identificacion', $numero)->first();

        if ($persona) {
            return response()->json([
                'encontrado' => true,
                'persona' => $persona
            ]);
        }

        return response()->json(['encontrado' => false]);
    }
}
