<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListaPreciosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $establecimientoId = session('establecimiento_id');
        $id = $this->route('listaPrecio')?->id; // route model binding

        return [
            'nombre' => [
                'required',
                'string',
                'max:150',
                Rule::unique('lista_precios', 'nombre')
                    ->where('id_establecimiento', $establecimientoId)
                    ->ignore($id),
            ],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.unique' => 'Ya existe una lista con ese nombre en este establecimiento.',
        ];
    }
}
