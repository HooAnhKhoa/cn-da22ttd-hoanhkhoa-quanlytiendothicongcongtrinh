<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        $project = Project::factory()->create();
        $startDate = $this->faker->dateTimeBetween('-3 months', '+1 month');
        $endDate = $this->faker->optional(0.8)->dateTimeBetween($startDate, '+6 months');
        
        return [
            'project_id' => $project->id,
            'site_code' => $this->faker->unique()->bothify('SITE-####'),
            'site_name' => $this->faker->words(2, true) . ' Site',
            'description' => $this->faker->paragraph(),
            'total_budget' => $this->faker->numberBetween(5000000, 50000000),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'on_hold', 'completed']),
        ];
    }

    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
            'progress_percent' => 0,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'progress_percent' => $this->faker->numberBetween(1, 99),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress_percent' => 100,
            'end_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }
}