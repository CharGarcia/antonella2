<?php

namespace App\Http\Controllers\empresa;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Vendedor;
use App\Models\Banco;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        return view('empresa.clientes.index');
    }

    public function getData(Request $request)
    {
        $query = Persona::with('vendedor')
            ->whereJsonContains('tipo', 'cliente');

        return DataTables::eloquent($query)
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
                                $query->whereHas('vendedor', function ($q) use ($searchValue) {
                                    $q->where('nombre', 'like', "%$searchValue%");
                                });
                                break;
                            case 6:
                                if ($searchValue !== '') {
                                    $estado = $searchValue == '1';
                                    $query->where('estado', $estado);
                                }
                                break;
                        }
                    }
                }
            })
            ->addColumn('acciones', function ($cliente) {
                return '<button class="btn btn-warning btn-sm editar-cliente" data-id="' . $cliente->id . '" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm eliminar-cliente" data-id="' . $cliente->id . '" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>';
            })
            ->editColumn('estado', function ($cliente) {
                return $cliente->estado == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })

            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }

    public function create()
    {
        $vendedores = Vendedor::pluck('nombre', 'id');
        $bancos = Banco::pluck('nombre', 'id');
        return view('empresa.clientes.create', compact('vendedores', 'bancos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_identificacion' => 'nullable|string|max:2',
            'numero_identificacion' => 'nullable|string|max:50',
            'telefono' => 'nullable|digits:10',
            'emails.*' => 'nullable|email',
            'direccion' => 'nullable|string',
            'id_vendedor' => 'nullable|exists:vendedores,id',
            'tipo_empresa' => 'nullable|string',
            'nombre_comercial' => 'nullable|string',
            'plazo_credito' => 'nullable|integer|min:0',
            'parte_relacionada' => 'nullable|boolean',
            'genero' => 'nullable|string',
            'estado' => 'nullable|boolean',
        ]);

        $data['email'] = collect($request->emails)->filter()->implode(',');
        $data['tipo'] = ['cliente'];
        //$data['id_establecimiento'] = auth()->user()->establecimiento_id ?? 1;
        //$data['id_user'] = auth()->id();
        $data['estado'] = $request->has('estado');

        Persona::create($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Persona $cliente)
    {
        abort_unless(in_array('cliente', $cliente->tipo), 404);

        $vendedores = Vendedor::pluck('nombre', 'id');
        $bancos = Banco::pluck('nombre', 'id');
        return view('empresa.clientes.edit', compact('cliente', 'vendedores', 'bancos'));
    }

    public function update(Request $request, Persona $cliente)
    {
        abort_unless(in_array('cliente', $cliente->tipo), 404);

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_identificacion' => 'nullable|string|max:50',
            'numero_identificacion' => 'nullable|string|max:50',
            'telefono' => 'nullable|digits:10',
            'emails.*' => 'nullable|email',
            'direccion' => 'nullable|string',
            'id_vendedor' => 'nullable|exists:vendedores,id',
            'tipo_empresa' => 'nullable|string',
            'nombre_comercial' => 'nullable|string',
            'plazo_credito' => 'nullable|integer|min:0',
            'parte_relacionada' => 'nullable|boolean',
            'id_banco' => 'nullable|exists:bancos,id',
            'tipo_cuenta' => 'nullable|string',
            'numero_cuenta' => 'nullable|string|max:30',
            'genero' => 'nullable|string',
            'fecha_nacimiento' => 'nullable|date',
            'estado' => 'nullable|boolean',
        ]);

        $data['email'] = collect($request->emails)->filter()->implode(',');
        $data['estado'] = $request->has('estado');

        $cliente->update($data);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Persona $cliente)
    {
        abort_unless(in_array('cliente', $cliente->tipo), 404);
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }
}
