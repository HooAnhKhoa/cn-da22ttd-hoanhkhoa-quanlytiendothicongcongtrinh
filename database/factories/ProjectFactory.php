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
        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        
        return [
            'project_name' => $this->faker->words(3, true) . ' Project',
            'owner_id' => User::factory()->owner(),
            'contractor_id' => User::factory()->contractor(),
            'engineer_id' => User::factory()->engineer(),
            'location' => $this->faker->city() . ', ' . $this->faker->state(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_budget' => $this->faker->randomFloat(2, 100000, 5000000),
            'description' => $this->faker->paragraph(3),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'on_hold']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}