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
        
        // 1. Lấy ngày tháng từ Task
        $start = $task->start_date;
        $end = $task->end_date ?? 'now';

        // 2. Kiểm tra logic: Nếu ngày bắt đầu lớn hơn ngày kết thúc, 
        // ép ngày bắt đầu lùi lại bằng ngày kết thúc để Faker không lỗi.
        if (strtotime($start) > strtotime($end)) {
            $start = $end;
        }

        // 3. Tạo reportedDate an toàn
        $reportedDate = $this->faker->dateTimeBetween($start, $end);
        
        return [
            'task_id' => $task->id,
            'reason' => $this->faker->paragraph(2),
            'delay_days' => $this->faker->numberBetween(1, 30),
            'reported_date' => $reportedDate,
            'responsible_engineer' => $engineer->id,
        ];
    }
}