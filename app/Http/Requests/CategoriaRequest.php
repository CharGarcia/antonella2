<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Asegúrate de controlar esto con políticas si es necesario
    }

    public function rules(): array
    {
        $establecimientoId = session('establecimiento_id');
        $nombre = strtolower(trim($this->input('nombre')));

        $uniqueNombre = Rule::unique('categorias')
            ->where(function ($query) use ($nombre, $establecimientoId) {
                return $query->whereRaw('LOWER(nombre) = ?', [$nombre])
                    ->where('id_establecimiento', $establecimientoId);
            });

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Excluir ID en caso de update
            $uniqueNombre->ignore($this->route('categoria')->id);
        }

        return [
            'nombre' => ['required', 'string', 'max:150', 'regex:/^[\pL\pN\-\_\.\,\(\)]+(\s[\pL\pN\-\_\.\,\(\)]+)*$/u', $uniqueNombre],
            'descripcion' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s\-\_\.\,\(\)]+$/u'],
            'estado' => 'required|in:activo,inactivo',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'     => 'El nombre es obligatorio.',
            'nombre.unique'       => 'Ya existe una categoría con ese nombre.',
            'nombre.regex'        => 'El nombre contiene caracteres no permitidos.',
            'descripcion.regex'   => 'La descripción contiene caracteres no permitidos.',
            'estado.required'     => 'El estado es obligatorio.',
            'estado.in'           => 'El estado debe ser "activo" o "inactivo".',
        ];
    }
}
