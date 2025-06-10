<?php

namespace App\Http\Controllers\Admin;

use App\Models\RetencionSri;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RetencionSriController extends Controller
{
    public function index()
    {
        $retenciones = RetencionSri::all();
        return view('admin.retenciones_sri.index', compact('retenciones'));
    }

    public function getData(Request $request)
    {
        $data = RetencionSri::query();

        // Filtro por estado desde la columna 6 del datatable
        $estado = $request->input('columns.6.search.value');
        if (!empty($estado)) {
            $data->where('status', $estado);
        }

        // 👉 APLICAMOS select() sobre el query ya filtrado
        $data = $data->select([
            'id',
            'codigo_retencion',
            'concepto',
            'observaciones',
            'porcentaje',
            'impuesto',
            'codigo_ats',
            'status',
            'vigencia_desde',
            'vigencia_hasta'
        ]);

        return datatables()->of($data)
            ->editColumn('status', function ($row) {
                if ($row->status === 'activo') {
                    return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>';
                } else {
                    return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
                }
            })
            ->addColumn('acciones', function ($row) {
                return '<button class="btn btn-sm btn-primary editar" data-id="' . $row->id . '"><i class="fas fa-edit"></i></button>';
            })
            ->rawColumns(['acciones', 'status'])
            ->make(true);
    }


    public function show($id)
    {
        return RetencionSri::findOrFail($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_retencion' => 'required|max:10|unique:retenciones_sri,codigo_retencion,' . $request->id,
            'concepto' => 'required|string|max:500',
            'porcentaje' => 'required|numeric|min:0',
            'impuesto' => 'required|in:RENTA,IVA',
            'codigo_ats' => 'required|max:10',
            'status' => 'required|in:activo,inactivo',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date|after_or_equal:vigencia_desde',
            'observaciones' => 'nullable|string|max:255',
        ]);
        $retencion = RetencionSri::create($request->all());

        return response()->json($retencion);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'codigo_retencion' => 'required|max:10|unique:retenciones_sri,codigo_retencion,' . $id,
            'concepto' => 'required|string|max:500',
            'porcentaje' => 'required|numeric|min:0',
            'impuesto' => 'required|in:RENTA,IVA',
            'codigo_ats' => 'required|max:10',
            'status' => 'required|in:activo,inactivo',
            'vigencia_desde' => 'nullable|date',
            'vigencia_hasta' => 'nullable|date|after_or_equal:vigencia_desde',
            'observaciones' => 'nullable|string|max:255', // 👈 importante
        ]);

        $retencion = RetencionSri::findOrFail($id);
        $retencion->update($request->all());

        return response()->json($retencion);
    }
}
