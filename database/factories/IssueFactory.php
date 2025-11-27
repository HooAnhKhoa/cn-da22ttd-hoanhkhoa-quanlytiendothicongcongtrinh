<?php

namespace Database\Factories;

use App\Models\Issue;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class IssueFactory extends Factory
{
    protected $model = Issue::class;

    public function definition(): array
    {
        $task = Task::inRandomOrder()->first() ?? Task::factory()->create();
        $reporter = User::inRandomOrder()->first() ?? User::factory()->create();
        
        // Sử dụng timestamp để đảm bảo an toàn
        $taskStart = Carbon::parse($task->start_date)->getTimestamp();
        $taskEnd = $task->end_date 
            ? Carbon::parse($task->end_date)->getTimestamp()
            : time(); // Sử dụng thời gian hiện tại nếu end_date null
        
        // Đảm bảo taskEnd không nhỏ hơn taskStart
        if ($taskEnd < $taskStart) {
            $taskEnd = $taskStart + (24 * 3600); // Thêm 24 giờ nếu cần
        }
        
        // Tạo date_reported trong khoảng thời gian của task, nhưng không vượt quá hiện tại
        $maxReportDate = min($taskEnd, time());
        $dateReportedTimestamp = $this->faker->numberBetween($taskStart, $maxReportDate);
        $dateReported = Carbon::createFromTimestamp($dateReportedTimestamp);
        
        // Tạo date_resolved (nếu có) sau date_reported và không vượt quá hiện tại
        $dateResolved = null;
        if ($this->faker->boolean(60)) { // 60% có date_resolved
            $minResolvedTimestamp = $dateReportedTimestamp + (1 * 3600); // Ít nhất 1 giờ sau
            $maxResolvedTimestamp = min($taskEnd, time());
            
            if ($minResolvedTimestamp < $maxResolvedTimestamp) {
                $dateResolvedTimestamp = $this->faker->numberBetween($minResolvedTimestamp, $maxResolvedTimestamp);
                $dateResolved = Carbon::createFromTimestamp($dateResolvedTimestamp);
            }
        }
        
        return [
            'task_id' => $task->id,
            'description' => $this->faker->paragraph(3),
            'severity' => $this->faker->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => $this->faker->randomElement(['open', 'in_progress', 'resolved', 'closed']),
            'reported_by' => $reporter->id,
            'date_reported' => $dateReported,
            'date_resolved' => $dateResolved,
        ];
    }
}