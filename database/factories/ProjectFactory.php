<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'project_name' => $this->faker->unique()->words(3, true) . ' Project ' . $this->faker->unique()->numberBetween(1, 100000),
            'owner_id' => User::factory(),
            'contractor_id' => User::factory(),
            'engineer_id' => User::factory(),
            'location' => $this->faker->city . ', ' . $this->faker->state,
            'start_date' => $this->faker->dateTimeBetween('-1 year', '+1 year'),
            'end_date' => $this->faker->optional()->dateTimeBetween('+1 year', '+2 years'),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['draft', 'pending_contract', 'in_progress', 'on_hold', 'completed']),
        ];
    }
}