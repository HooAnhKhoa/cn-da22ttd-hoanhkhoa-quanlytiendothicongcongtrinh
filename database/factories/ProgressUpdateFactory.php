<?php

namespace Database\Factories;

use App\Models\ProgressUpdate;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProgressUpdateFactory extends Factory
{
    protected $model = ProgressUpdate::class;

    public function definition(): array
{
    $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
    $user = User::inRandomOrder()->first() ?? User::factory()->create();
    
    // Đảm bảo date nằm trong khoảng thời gian của task
    $date = $this->faker->dateTimeBetween($task->start_date, $task->end_date ?? 'now');
    
    return [
        'task_id' => $task->id,
        'date' => $date,
        'progress_percent' => $this->faker->numberBetween(0, 100),
        'description' => $this->faker->paragraph(2),
        'attached_files' => $this->faker->optional(0.3)->randomElement([['file1.pdf'], ['image1.jpg', 'report.docx']]),
        'created_by' => $user->id,
        'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
    ];
}
}