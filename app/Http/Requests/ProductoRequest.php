<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $establecimientoId = session('establecimiento_id');
        $productoId = optional($this->route('producto'))->id;

        return [
            'codigo' => [
                'bail', // Detiene en el primer error
                'required',
                'string',
                'max:200',
                // Solo letras, números, guiones, guiones bajos, paréntesis, puntos, comas, y espacios entre palabras
                'regex:/^[\pL\pN\-\_\.\,\(\)]+(\s[\pL\pN\-\_\.\,\(\)]+)*$/u',
                Rule::unique('productos')->where(function ($query) use ($establecimientoId) {
                    return $query->where('id_establecimiento', $establecimientoId);
                })->ignore($productoId),
            ],
            'descripcion' => [
                'bail',
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\pN\-\_\.\,\(\)]+(\s[\pL\pN\-\_\.\,\(\)]+)*$/u',
                Rule::unique('productos')->where(function ($query) use ($establecimientoId) {
                    return $query->where('id_establecimiento', $establecimientoId);
                })->ignore($productoId),
            ],
            'tipo_id' => ['required', 'string', 'max:150'],
            'tarifa_iva_id' => ['required', 'string', 'max:150'],
            'precio_base' => ['required', 'string', 'max:150'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required'     => 'El código del producto es obligatorio.',
            'codigo.string'       => 'El código debe ser una cadena de texto.',
            'codigo.max'          => 'El código no debe exceder los 200 caracteres.',
            'codigo.regex'        => 'El código contiene caracteres no válidos o espacios incorrectos.',
            'codigo.unique'       => 'Ya existe un producto con ese código en este establecimiento.',

            'descripcion.required' => 'El nombre o descripción del producto es obligatorio.',
            'descripcion.string'  => 'La descripción debe ser una cadena de texto.',
            'descripcion.max'     => 'La descripción no debe exceder los 255 caracteres.',
            'descripcion.regex'   => 'La descripción contiene caracteres no válidos o espacios incorrectos.',
            'descripcion.unique'  => 'Ya existe un producto con esa descripción en este establecimiento.',

            'tipo_id.required'       => 'Debe seleccionar un tipo de producto.',
            'tarifa_iva_id.required' => 'Debe seleccionar una tarifa de IVA.',
            'precio_base.required'   => 'Debe ingresar un precio base.',
            'estado.required'        => 'El estado es obligatorio.',
            'estado.in'              => 'El estado debe ser "activo" o "inactivo".',
        ];
    }
}
