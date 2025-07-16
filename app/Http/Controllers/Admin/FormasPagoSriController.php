<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\FormasPagoSri;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class FormasPagoSriController extends Controller
{
    public function index()
    {
        return view('admin.formas_pago_sri.index');
    }

    public function getData(Request $request)
    {
        $FormasPagoSri = FormasPagoSri::query();

        return DataTables::of($FormasPagoSri)
            ->addColumn('acciones', function ($FormasPagoSri) {
                return '
                <button class="btn btn-warning btn-sm editar-formas_pago_sri" data-id="' . $FormasPagoSri->id . '" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>';
            })
            ->editColumn('estado', function ($FormasPagoSri) {
                return $FormasPagoSri->estado == 1
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activa</span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactiva</span>';
            })
            ->rawColumns(['acciones', 'estado'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:formas_pago_sri,codigo',
            'descripcion' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);

        FormasPagoSri::create($request->all());
        return response()->json(['message' => 'Forma de pago creada correctamente']);
    }

    public function show(FormasPagoSri $formaPagoSri)
    {
        return response()->json($formaPagoSri);
    }

    public function update(Request $request, FormasPagoSri $formaPagoSri)
    {
        $request->validate([
            'codigo' => 'required|unique:formas_pago_sri,codigo,' . $formaPagoSri->id,
            'descripcion' => 'required|string|max:50',
            'estado' => 'required|boolean',
        ]);

        $formaPagoSri->update($request->all());
        return response()->json(['message' => 'Forma de pago actualizada correctamente']);
    }
}
