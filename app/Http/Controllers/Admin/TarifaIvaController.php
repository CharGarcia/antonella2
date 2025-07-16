<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\TarifaIva;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;


class TarifaIvaController extends Controller
{
    public function index()
    {
        return view('admin.tarifa_iva.index');
    }

    public function getData(Request $request)
    {
        $tarifas = TarifaIva::query();

        return DataTables::of($tarifas)
            ->addColumn('acciones', function ($tarifas) {
                return '
                <button class="btn btn-warning btn-sm editar-tarifa_iva" data-id="' . $tarifas->id . '" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm eliminar-tarifa_iva" data-id="' . $tarifas->id . '" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                </button>';
            })
            ->editColumn('estado', function ($tarifas) {
                return $tarifas->estado == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:tarifa_iva,codigo',
            'descripcion' => 'required|string|max:50',
            'porcentaje' => 'required|numeric|min:0',
        ]);

        TarifaIva::create($request->all());
        return response()->json(['message' => 'Tarifa creada correctamente']);
    }

    public function update(Request $request, TarifaIva $tarifa)
    {
        $request->validate([
            'codigo' => 'required|unique:tarifa_iva,codigo,' . $tarifa->id,
            'descripcion' => 'required|string|max:50',
            'porcentaje' => 'required|numeric|min:0',
        ]);

        $tarifa->update($request->all());
        return response()->json(['message' => 'Tarifa actualizada correctamente']);
    }

    public function destroy(TarifaIva $tarifa)
    {
        $tarifa->delete();
        return response()->json(['message' => 'Tarifa eliminada correctamente']);
    }

    public function show(TarifaIva $tarifa)
    {
        return response()->json($tarifa);
    }
}
