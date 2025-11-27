<?php

namespace Database\Factories;

use App\Models\Delay;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DelayFactory extends Factory
{
    protected $model = Delay::class;

    public function definition(): array
{
    $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
    $engineer = User::where('user_type', 'engineer')->inRandomOrder()->first() ?? User::factory()->engineer()->create();
    
    // Đảm bảo reported_date nằm trong khoảng thời gian của task
    $reportedDate = $this->faker->dateTimeBetween($task->start_date, $task->end_date ?? 'now');
    
    return [
        'task_id' => $task->id,
        'reason' => $this->faker->paragraph(2),
        'delay_days' => $this->faker->numberBetween(1, 30),
        'reported_date' => $reportedDate,
        'responsible_engineer' => $engineer->id,
    ];
}
}