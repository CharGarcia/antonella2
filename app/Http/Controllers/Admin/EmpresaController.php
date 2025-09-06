<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Empresa;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class EmpresaController extends Controller
{
    public function index()
    {
        return view('admin.empresas.index');
    }

    public function getData(Request $request)
    {
        $empresas = Empresa::query();

        return DataTables::of($empresas)
            ->addColumn('acciones', function ($empresas) {
                $btnEditar = '<button class="btn btn-warning btn-sm editar-empresa mr-2" data-id="' . $empresas->id . '" title="Editar">
                            <i class="fas fa-edit"></i>
                          </button>';
                return '<div class="d-flex justify-content-center gap-1">' . $btnEditar . '</div>';
            })

            ->addColumn('tipo_contribuyente', function ($empresa) {
                return $empresa->tipo_contribuyente === '01' ? 'Persona natural' : 'Sociedad';
            })

            ->filterColumn('tipo_contribuyente', function ($empresas, $keyword) {
                $empresas->where(function ($q) use ($keyword) {
                    if (stripos($keyword, 'persona') !== false) {
                        $q->where('tipo_contribuyente', '01');
                    } elseif (stripos($keyword, 'sociedad') !== false) {
                        $q->where('tipo_contribuyente', '02');
                    }
                });
            })

            // 👇 Mapea el régimen (puedes modificar según tipo de dato guardado)
            ->editColumn('regimen', function ($empresas) {
                return match ((int)$empresas->regimen) {
                    1 => 'General',
                    2 => 'Rimpe emprendedor',
                    3 => 'Rimpe negocio popular',
                    default => 'No definido',
                };
            })
            ->filterColumn('regimen', function ($empresas, $keyword) {
                $empresas->where(function ($q) use ($keyword) {
                    if (stripos($keyword, 'general') !== false) {
                        $q->where('regimen', 1);
                    } elseif (stripos($keyword, 'emprendedor') !== false) {
                        $q->where('regimen', 2);
                    } elseif (stripos($keyword, 'popular') !== false) {
                        $q->where('regimen', 3);
                    }
                });
            })
            ->editColumn('estado', function ($empresas) {
                if ($empresas->estado == 'activo') {
                    return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activa</span>';
                } else {
                    return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactiva</span>';
                }
            })

            ->rawColumns(['acciones', 'estado', 'tipo_contribuyente', 'regimen'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:500',
            'ruc' => 'required|string|max:13|unique:empresas,ruc',
            'email' => 'nullable|email',
            'direccion' => 'required|string|max:500',
        ], [
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social no debe exceder 500 caracteres.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.max' => 'El RUC no debe exceder 13 caracteres.',
            'ruc.unique' => 'Este RUC ya está registrado.',
            'email.email' => 'El email debe ser válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no debe exceder 500 caracteres.',
        ]);

        Empresa::create($request->all());

        return response()->json(['success' => true, 'message' => 'Empresa creada correctamente.']);
    }


    public function show(Empresa $empresa)
    {
        return response()->json($empresa);
    }


    public function update(Request $request, Empresa $empresa)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:13|unique:empresas,ruc,' . $empresa->id,
            'email' => 'nullable|email',
            'direccion' => 'required|string|max:500',
        ], [
            'razon_social.required' => 'La razón social es obligatoria.',
            'razon_social.max' => 'La razón social no debe exceder 500 caracteres.',
            'ruc.required' => 'El RUC es obligatorio.',
            'ruc.max' => 'El RUC no debe exceder 13 caracteres.',
            'ruc.unique' => 'Este RUC ya está registrado.',
            'email.email' => 'El email debe ser válido.',
            'direccion.required' => 'La dirección es obligatoria.',
            'direccion.max' => 'La dirección no debe exceder 500 caracteres.',
        ]);

        $empresa->update($request->all());

        return response()->json(['success' => true, 'message' => 'Empresa actualizada correctamente.']);
    }

    public function buscar(Request $request)
    {
        $search = $request->input('q');

        $empresas = Empresa::whereRaw("razon_social ILIKE ?", ["%{$search}%"])
            ->select('id', 'razon_social')
            ->limit(10)
            ->get();

        return response()->json($empresas);
    }
}
