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
        
        // Đảm bảo start_time nằm trong khoảng thời gian của task và có đủ thời gian cho end_time
        $taskStart = Carbon::parse($task->start_date);
        $taskEnd = $task->end_date ? Carbon::parse($task->end_date) : Carbon::now();
        
        // Đảm bảo taskEnd ít nhất 8 giờ sau taskStart
        if ($taskEnd->diffInHours($taskStart) < 8) {
            $taskEnd = $taskStart->copy()->addHours(24); // Thêm 24 giờ nếu không đủ
        }
        
        // Tạo start_time với đủ khoảng trống cho end_time
        $maxStartTime = $taskEnd->copy()->subHours(8); // Đảm bảo có ít nhất 8 giờ trước taskEnd
        $startTime = $this->faker->dateTimeBetween(
            $taskStart, 
            $maxStartTime
        );
        
        // Tạo end_time trong khoảng từ start_time đến taskEnd, nhưng ít nhất 1 giờ sau start_time
        $minEndTime = (new Carbon($startTime))->addHours(1);
        $endTime = $this->faker->dateTimeBetween(
            $minEndTime, 
            $taskEnd
        );
        
        return [
            'task_id' => $task->id,
            'equipment_id' => $equipment->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'engineer_id' => $engineer->id,
        ];
    }
}