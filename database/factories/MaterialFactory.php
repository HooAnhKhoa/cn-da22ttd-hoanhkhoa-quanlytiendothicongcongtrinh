<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    protected $model = Material::class;

    public function definition(): array
    {
        return [
            'materials_name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Cement', 'Steel', 'Brick', 'Sand', 'Gravel', 'Wood', 'Pipe', 'Wire']),
            'unit' => $this->faker->randomElement(['kg', 'ton', 'm³', 'm', 'piece', 'bag']),
            'type' => $this->faker->randomElement(['construction', 'electrical', 'plumbing', 'finishing']),
            'supplier' => $this->faker->company(),
        ];
    }
}