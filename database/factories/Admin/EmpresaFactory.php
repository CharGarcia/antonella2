<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Empresa;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaFactory extends Factory
{
    protected $model = Empresa::class;

    public function definition(): array
    {
        $tiposContribuyente = ['02', '01'];
        $regimenes = ['1', '2', '3'];
        $contabilidad = ['SI', 'NO'];

        return [
            'razon_social'           => strtoupper($this->faker->company()),
            'tipo_contribuyente'     => $this->faker->randomElement($tiposContribuyente),
            'ruc'                    => $this->faker->unique()->numerify('#############'), // 13 dígitos
            'regimen'                => $this->faker->randomElement($regimenes),
            'contabilidad'           => $this->faker->randomElement($contabilidad),
            'contribuyente_especial' => $this->faker->randomElement($contabilidad),
            'agente_retencion'       => $this->faker->randomElement($contabilidad),
            'nombre_rep_leg'         => $this->faker->name(),
            'cedula_rep_leg'         => $this->faker->unique()->numerify('##########'), // 10 dígitos
            'nombre_contador'        => $this->faker->name(),
            'ruc_contador'           => $this->faker->unique()->numerify('#############'),
            'email'                  => $this->faker->unique()->companyEmail(),
            'telefono'               => $this->faker->numerify('0#########'),
            'direccion'              => $this->faker->address(),
            'archivo_firma'          => null,
            'password_firma'         => null,
            'estado'                 => 'activo',
        ];
    }
}
