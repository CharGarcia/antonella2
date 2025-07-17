<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Empresa\Personas\Persona;
use Illuminate\Support\Carbon;

class CompradorController extends Controller
{
    public function index()
    {
        return view('empresa.compradores.index');
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

        $compradores = Persona::where('id_establecimiento', $establecimientoId)
            ->whereJsonContains('tipo', ['comprador'])
            ->with('datosComprador');

        return DataTables::eloquent($compradores)
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
                                    $query->whereHas('datosComprador', function ($q) use ($estado) {
                                        $q->where('estado', $estado);
                                    });
                                }
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($comprador) use ($permisos) {
                $botones = '<div class="d-flex" style="gap: 0.25rem;">';
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('modificar', $permisos)) {
                    $botones .= '<button class="btn btn-warning btn-sm editar-comprador" data-id="' . $comprador->id . '" title="Editar"><i class="fas fa-edit"></i></button>';
                }
                if (\App\Helpers\PermisosHelper::puedeRealizarAccion('eliminar', $permisos)) {
                    $botones .= '<button class="btn btn-danger btn-sm eliminar-comprador" data-id="' . $comprador->id . '" title="Eliminar"><i class="fas fa-trash-alt"></i></button>';
                }
                $botones .= '</div>';
                return $botones;
            })
            ->addColumn('estado', function ($comprador) {
                $estado = $comprador->datosComprador->estado ?? 'activo';
                return $estado === 'activo'
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }

    public function destroy($id)
    {
        $comprador = Persona::findOrFail($id);

        if (!in_array('comprador', $comprador->tipo ?? [])) {
            return response()->json(['message' => 'No es un comprador válido.'], 400);
        }

        $tipos = array_filter($comprador->tipo, fn($tipo) => $tipo !== 'comprador');

        if (!empty($tipos)) {
            $comprador->update(['tipo' => array_values($tipos)]);
        } else {
            $comprador->delete();
        }

        return response()->json(['message' => 'Comprador eliminado']);
    }

    public function store(Request $request)
    {
        return $this->saveOrUpdate($request);
    }

    public function update(Request $request, $id)
    {
        return $this->saveOrUpdate($request, $id);
    }

    private function saveOrUpdate(Request $request, $id = null)
    {
        $isUpdate = $id !== null;
        $persona = $isUpdate ? Persona::findOrFail($id) : null;

        $validator = Validator::make($request->all(), [
            'tipo_identificacion' => 'required|string',
            'numero_identificacion' => [
                'required',
                'string',
                Rule::unique('personas')->ignore($id)->where(function ($query) {
                    return $query->where('id_establecimiento', session('establecimiento_id'))
                        ->whereJsonContains('tipo', 'comprador');
                }),
            ],
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'required|string',
            'direccion' => 'nullable|string|max:255',
            'estado' => 'required|in:activo,inactivo',

            // Tab comercial
            'codigo_interno' => 'nullable|string|max:100',
            'perfil' => 'nullable|string|max:100',
            'zona' => 'nullable|string|max:100',
            'inicio_relacion' => 'nullable|string',
            'pais' => 'nullable|string|max:50',
            'provincia' => 'nullable|string|max:50',
            'ciudad' => 'nullable|string|max:50',
            'informacion_adicional' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        // Normalización
        $data['email'] = strtolower(trim($data['email']));
        foreach (['nombre', 'direccion', 'provincia', 'ciudad', 'pais'] as $campo) {
            if (!empty($data[$campo])) {
                $data[$campo] = strtoupper(trim($data[$campo]));
            }
        }

        // Parsear fecha
        if (!empty($data['inicio_relacion'])) {
            try {
                $data['inicio_relacion'] = Carbon::createFromFormat('d/m/Y', $data['inicio_relacion'])->format('Y-m-d');
            } catch (\Exception $e) {
                return response()->json(['errors' => ['inicio_relacion' => ['Formato de fecha inválido.']]], 422);
            }
        }

        if ($isUpdate) {
            $tipos = $persona->tipo ?? [];
            if (!in_array('comprador', $tipos)) {
                $tipos[] = 'comprador';
            }
            $persona->tipo = array_unique($tipos);
            $persona->fill($data)->save();

            $persona->datosComprador()->updateOrCreate([], [
                'estado' => $data['estado'],
                'codigo_interno' => $data['codigo_interno'] ?? null,
                'perfil' => $data['perfil'] ?? null,
                'zona' => $data['zona'] ?? null,
                'inicio_relacion' => $data['inicio_relacion'] ?? null,
                'informacion_adicional' => $data['informacion_adicional'] ?? null,
            ]);
        } else {
            DB::transaction(function () use ($data) {
                $persona = Persona::where('numero_identificacion', trim($data['numero_identificacion']))
                    ->where('id_establecimiento', session('establecimiento_id'))
                    ->first();

                if ($persona) {
                    $tipos = $persona->tipo;
                    if (!in_array('comprador', $tipos)) {
                        $tipos[] = 'comprador';
                        $persona->tipo = array_unique($tipos);
                    }
                    $persona->fill($data)->save();
                } else {
                    $persona = Persona::create(array_merge($data, [
                        'id_user' => Auth::id(),
                        'id_establecimiento' => session('establecimiento_id'),
                        'tipo' => ['comprador'],
                    ]));
                }

                $persona->datosComprador()->updateOrCreate([], [
                    'estado' => $data['estado'],
                    'codigo_interno' => $data['codigo_interno'] ?? null,
                    'perfil' => $data['perfil'] ?? null,
                    'zona' => $data['zona'] ?? null,
                    'inicio_relacion' => $data['inicio_relacion'] ?? null,
                    'informacion_adicional' => $data['informacion_adicional'] ?? null,
                    'fecha_registro' => $persona->datosComprador()->exists()
                        ? $persona->datosComprador->fecha_registro
                        : Carbon::now()->toDateString(),
                ]);
            });
        }

        return response()->json([
            'message' => $isUpdate
                ? 'Comprador actualizado correctamente.'
                : 'Comprador registrado correctamente.'
        ]);
    }



    public function edit(Persona $comprador)
    {
        $comprador->load('datosComprador');
        $comprador->datosComprador?->append('inicio_relacion_formatted');
        return response()->json($comprador);
    }
}
