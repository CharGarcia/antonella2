<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Menu;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function index()
    {
        return view('admin.menus.index');
    }

    public function getMenu()
    {
        $menus = Menu::query(); // trae todos los menús principales

        return DataTables::of($menus)
            ->addColumn('acciones', function ($menu) {
                $btnEditar = '<button class="btn btn-warning btn-sm editar-menu mr-2" data-id="' . $menu->id . '" title="Editar">
                            <i class="fas fa-edit"></i>
                          </button>';

                $btnEliminar = '<button class="btn btn-danger btn-sm eliminar-menu mr-2" data-id="' . $menu->id . '" title="Eliminar">
                              <i class="fas fa-trash-alt"></i>
                            </button>';

                return '<div class="d-flex justify-content-center gap-1">' . $btnEditar . ' ' . $btnEliminar . '</div>';
            })
            ->editColumn('icono', function ($menu) {
                return '<i class="' . e($menu->icono) . '" title="' . e($menu->icono) . '"></i>';
            })
            ->editColumn('estado', function ($menu) {
                if ($menu->estado == 'activo') {
                    return '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Activo</span>';
                } else {
                    return '<span class="badge badge-danger"><i class="fas fa-times-circle"></i> Inactivo</span>';
                }
            })

            ->rawColumns(['acciones', 'icono', 'estado'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:100',
            'orden' => 'nullable|integer',
            //'activo' => 'required|boolean'
        ]);

        Menu::create($request->all());

        return response()->json(['success' => true, 'message' => 'Menú creado correctamente']);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu);
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'icono' => 'nullable|string|max:100',
            'orden' => 'nullable|integer',
            //'activo' => 'required|boolean'
        ]);

        $menu->update($request->all());

        return response()->json(['success' => true, 'message' => 'Menú actualizado correctamente']);
    }

    public function destroy(Menu $menu)
    {
        $menu->update(['activo' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Menú desactivado correctamente.'
        ]);
    }
}
