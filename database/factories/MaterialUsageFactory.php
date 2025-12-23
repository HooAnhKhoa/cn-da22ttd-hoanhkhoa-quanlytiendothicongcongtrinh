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
        
        // 1. Lấy mốc thời gian từ Task
        $start = $task->start_date;
        $end = $task->end_date ?? 'now';

        // 2. Kiểm tra và sửa lỗi logic ngày tháng nếu có
        // (Đảm bảo start không bao giờ lớn hơn end)
        if (strtotime($start) > strtotime($end)) {
            $start = $end;
        }
        
        // 3. Tạo usageDate an toàn
        $usageDate = $this->faker->dateTimeBetween($start, $end);
        
        return [
            'task_id' => $task->id,
            'material_id' => $material->id,
            'quantity' => $this->faker->randomFloat(2, 1, 1000),
            'usage_date' => $usageDate,
        ];
    }
}