<?php

namespace Database\Factories;

use App\Models\Site;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $site = Site::inRandomOrder()->first() ?? Site::factory()->create();
        
        // Đảm bảo start_date nằm trong khoảng thời gian của site
        $startDate = $this->faker->dateTimeBetween($site->start_date, $site->end_date ?? '+1 month');
        
        // Đảm bảo end_date sau start_date và không vượt quá site end_date
        $maxEndDate = $site->end_date ?? date('Y-m-d', strtotime('+60 days', $startDate->getTimestamp()));
        $endDate = $this->faker->dateTimeBetween($startDate, $maxEndDate);
        
        $plannedDuration = $this->faker->numberBetween(5, 60);
        
        return [
            'site_id' => $site->id,
            'parent_id' => null,
            'task_name' => $this->faker->words(3, true) . ' Task',
            'description' => $this->faker->paragraph(2),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'planned_duration' => $plannedDuration,
            'actual_duration' => $this->faker->numberBetween($plannedDuration - 5, $plannedDuration + 10),
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'on_hold']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}