<?php

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'equipment_name' => $this->faker->word() . ' ' . $this->faker->randomElement(['Excavator', 'Crane', 'Bulldozer', 'Concrete Mixer', 'Generator', 'Compressor']),
            'type' => $this->faker->randomElement(['heavy', 'light', 'electrical', 'mechanical']),
            'serial' => $this->faker->unique()->bothify('EQ-#####-??'),
            'status' => $this->faker->randomElement(['available', 'in_use', 'under_maintenance']),
            'location' => $this->faker->city() . ' Warehouse',
        ];
    }
}