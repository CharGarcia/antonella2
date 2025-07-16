<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Empresa\Personas\Persona;

class PersonaController extends Controller
{
    public function buscarPorIdentificacion(Request $request)
    {
        $numero = $request->numero_identificacion;

        $persona = Persona::buscarPorIdentificacion($numero);

        return response()->json([
            'encontrado' => (bool) $persona,
            'persona' => $persona
        ]);
    }
}
