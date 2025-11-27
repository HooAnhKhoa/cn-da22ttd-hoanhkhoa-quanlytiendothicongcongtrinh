<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Site;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiteFactory extends Factory
{
    protected $model = Site::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $startDate = $this->faker->dateTimeBetween($project->start_date, $project->end_date ?? '+3 months');
        
        // Đảm bảo end_date không null và sau start_date
        $endDate = $this->faker->dateTimeBetween($startDate, $project->end_date ?? '+1 year');
        
        return [
            'project_id' => $project->id,
            'site_name' => $this->faker->words(2, true) . ' Site',
            'description' => $this->faker->paragraph(2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'on_hold']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}