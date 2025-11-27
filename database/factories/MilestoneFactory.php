<?php

namespace Database\Factories;

use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class MilestoneFactory extends Factory
{
    protected $model = Milestone::class;

    public function definition(): array
    {
        $project = Project::inRandomOrder()->first() ?? Project::factory()->create();
        $targetDate = $this->faker->dateTimeBetween($project->start_date, $project->end_date ?? '+1 year');
        
        return [
            'project_id' => $project->id,
            'milestone_name' => $this->faker->words(3, true) . ' Milestone',
            'description' => $this->faker->paragraph(2),
            'target_date' => $targetDate,
            'completed_date' => $this->faker->optional(0.7)->dateTimeBetween($project->start_date, $targetDate),
            'status' => $this->faker->randomElement(['pending', 'achieved', 'missed']),
            // KHÔNG thêm created_at và updated_at
        ];
    }
}