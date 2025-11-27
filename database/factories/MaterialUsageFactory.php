<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\MaterialUsage;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialUsageFactory extends Factory
{
    protected $model = MaterialUsage::class;

    public function definition(): array
{
    $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
    $material = Material::inRandomOrder()->first() ?? Material::factory()->create();
    
    // Đảm bảo usage_date nằm trong khoảng thời gian của task
    $usageDate = $this->faker->dateTimeBetween($task->start_date, $task->end_date ?? 'now');
    
    return [
        'task_id' => $task->id,
        'material_id' => $material->id,
        'quantity' => $this->faker->randomFloat(2, 1, 1000),
        'usage_date' => $usageDate,
    ];
}
}