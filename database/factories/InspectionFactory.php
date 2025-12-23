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
        
        // 1. Lấy ngày từ Task
        $start = $task->start_date;
        $end = $task->end_date ?? 'now';

        // 2. Kiểm tra logic an toàn: Nếu Start > End, ép Start = End
        if (strtotime($start) > strtotime($end)) {
            $start = $end;
        }

        // 3. Tạo ngày kiểm tra (inspection date) an toàn
        $date = $this->faker->dateTimeBetween($start, $end);
        
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