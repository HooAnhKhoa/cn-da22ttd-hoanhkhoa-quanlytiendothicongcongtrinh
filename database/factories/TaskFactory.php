<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $site = Site::inRandomOrder()->first() ?? Site::factory()->create();
        $engineers = User::where('user_type', 'engineer')->get();
        
        $startDate = $this->faker->dateTimeBetween('-2 months', '+1 month');
        // Tạo end_date cách start_date từ 5-30 ngày
        $endDate = $this->faker->dateTimeBetween($startDate, (clone $startDate)->modify('+30 days'));
        
        return [
            'site_id' => $site->id,
            'parent_id' => null,
            'assigned_engineer_id' => $engineers->isNotEmpty() ? $engineers->random()->id : User::factory()->create(['user_type' => 'engineer'])->id,
            'task_code' => 'TASK-' . $this->faker->unique()->numberBetween(1000, 9999),
            'task_name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'task_budget' => $this->faker->numberBetween(100000, 5000000),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'pending_review', 'completed']),
            
            // Review info
            'owner_review' => $this->faker->optional(0.3)->paragraph(),
            'owner_rating' => $this->faker->optional(0.4)->numberBetween(1, 5),
            'is_approved' => $this->faker->boolean(30),
            'approved_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'approved_by' => $this->faker->optional(0.3)->passthrough(
                User::where('user_type', 'owner')->first()?->id ?? User::factory()->create(['user_type' => 'owner'])->id
            ),
        ];
    }

    // --- CÁC PHƯƠNG THỨC HỖ TRỢ SEEDER (Đã phục hồi và sửa lỗi) ---

    public function withValidDates(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', '+1 month');
            $days = $this->faker->numberBetween(5, 30);
            $endDate = (clone $startDate)->modify("+{$days} days");
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                // Không set planned_duration nữa vì cột đã xóa
            ];
        });
    }

    public function withParent(): static
    {
        return $this->state(function (array $attributes) {
            $parentTask = Task::factory()->create();
            
            return [
                'parent_id' => $parentTask->id,
                'site_id' => $parentTask->site_id,
                'start_date' => $parentTask->start_date, // Start cùng cha
                'task_budget' => $parentTask->task_budget * 0.3,
            ];
        });
    }

    public function planned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'planned',
            'progress_percent' => 0,
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'progress_percent' => $this->faker->numberBetween(1, 99),
            'is_approved' => false,
        ]);
    }

    public function pendingReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending_review',
            'progress_percent' => 100,
            'is_approved' => false,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'progress_percent' => 100,
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => User::where('user_type', 'owner')->first()?->id,
        ]);
    }
}