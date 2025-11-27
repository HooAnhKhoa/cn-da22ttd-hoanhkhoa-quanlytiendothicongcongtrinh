<?php

namespace Database\Factories;

use App\Models\Inspection;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class InspectionFactory extends Factory
{
    protected $model = Inspection::class;

    public function definition(): array
{
    $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
    $engineer = User::where('user_type', 'engineer')->inRandomOrder()->first() ?? User::factory()->engineer()->create();
    
    // Đảm bảo date nằm trong khoảng thời gian của task
    $date = $this->faker->dateTimeBetween($task->start_date, $task->end_date ?? 'now');
    
    return [
        'task_id' => $task->id,
        'engineer_id' => $engineer->id,
        'result' => $this->faker->randomElement(['pass', 'fail', 'rework']),
        'notes' => $this->faker->paragraph(2),
        'date' => $date,
        'attached_files' => $this->faker->optional(0.4)->randomElement([['inspection_report.pdf'], ['photos.zip']]),
    ];
}
}