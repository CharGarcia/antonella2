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
        $id = $this->route('listaPrecio')?->id;

        return [
            'nombre' => [
                'bail',
                'required',
                'string',
                'max:150',
                // Acepta letras, números, paréntesis, guiones, puntos, comas, espacios normales (no al final)
                'regex:/^[\pL\pN\-\_\.\,\(\)]+(\s[\pL\pN\-\_\.\,\(\)]+)*$/u',
                Rule::unique('lista_precios', 'nombre')
                    ->where(function ($query) use ($establecimientoId) {
                        return $query->where('id_establecimiento', $establecimientoId);
                    })->ignore($id),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:500',
                'regex:/^[\pL\pN\-\_\.\,\(\)]+(\s[\pL\pN\-\_\.\,\(\)]+)*$/u',
            ],
            'estado' => [
                'required',
                Rule::in(['activo', 'inactivo']),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'     => 'El nombre es obligatorio.',
            'nombre.string'       => 'El nombre debe ser una cadena de texto.',
            'nombre.max'          => 'El nombre no debe superar los 150 caracteres.',
            'nombre.regex'        => 'El nombre contiene caracteres no válidos o espacios al final.',
            'nombre.unique'       => 'Ya existe una lista con ese nombre en este establecimiento.',

            'descripcion.string'  => 'La descripción debe ser texto.',
            'descripcion.max'     => 'La descripción no debe superar los 500 caracteres.',
            'descripcion.regex'   => 'La descripción contiene caracteres no válidos o espacios al final.',

            'estado.required'     => 'El estado es obligatorio.',
            'estado.in'           => 'El estado debe ser "activo" o "inactivo".',
        ];
    }
}
