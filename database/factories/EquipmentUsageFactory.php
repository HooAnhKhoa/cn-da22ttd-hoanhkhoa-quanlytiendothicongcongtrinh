<?php

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\EquipmentUsage;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class EquipmentUsageFactory extends Factory
{
    protected $model = EquipmentUsage::class;

    public function definition(): array
    {
        $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
        $equipment = Equipment::inRandomOrder()->first() ?? Equipment::factory()->create();
        $engineer = User::where('user_type', 'engineer')->inRandomOrder()->first() ?? User::factory()->engineer()->create();
        
        // 1. Lấy mốc thời gian cơ bản
        $taskStart = Carbon::parse($task->start_date);
        $taskEnd = $task->end_date ? Carbon::parse($task->end_date) : Carbon::now();

        // 2. CHỐT CHẶN 1: Đảm bảo taskEnd phải sau taskStart
        if ($taskStart->gt($taskEnd)) {
            $taskEnd = $taskStart->copy()->addHours(24);
        }

        // 3. Tính toán maxStartTime (Điểm muộn nhất có thể bắt đầu sử dụng máy)
        // Chúng ta trừ đi 2 giờ để dành chỗ cho end_time
        $maxStartTime = $taskEnd->copy()->subHours(2);

        // 4. CHỐT CHẶN 2: Nếu sau khi trừ, maxStartTime lại nhỏ hơn taskStart
        // thì ta ép maxStartTime bằng taskEnd và lùi taskStart lại một chút
        if ($taskStart->gt($maxStartTime)) {
            $maxStartTime = $taskEnd;
            $taskStart = $maxStartTime->copy()->subMinutes(30);
        }

        // Tạo start_time an toàn
        $startTime = $this->faker->dateTimeBetween($taskStart, $maxStartTime);
        
        // 5. Tạo minEndTime (ít nhất 30 phút sau khi bắt đầu)
        $minEndTime = Carbon::parse($startTime)->addMinutes(30);
        
        // 6. CHỐT CHẶN 3: Đảm bảo minEndTime không vượt quá taskEnd
        if ($minEndTime->gt($taskEnd)) {
            $taskEnd = $minEndTime->copy()->addMinutes(30);
        }

        $endTime = $this->faker->dateTimeBetween($minEndTime, $taskEnd);
        
        return [
            'task_id' => $task->id,
            'equipment_id' => $equipment->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'engineer_id' => $engineer->id,
        ];
    }
}