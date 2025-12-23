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
        $site = Site::factory()->create();
        $engineers = User::where('user_type', 'engineer')->get();
        
        // Đảm bảo start_date hợp lệ
        $startDate = $this->faker->dateTimeBetween('-2 months', '+1 month');
        
        // Đảm bảo planned_duration là số dương
        $plannedDuration = $this->faker->numberBetween(5, 30); // từ 5 đến 30 ngày
        
        // Tạo end_date dựa trên start_date và planned_duration
        $endDate = $this->faker->dateTimeBetween(
            $startDate, 
            $startDate->format('Y-m-d') . " +{$plannedDuration} days"
        );
        
        // Đảm bảo actual_duration không lớn hơn quá nhiều so với planned_duration
        $actualDuration = $this->faker->optional(0.7)->numberBetween(
            max(1, $plannedDuration - 5), 
            $plannedDuration + 5
        );
        
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
            'planned_duration' => $plannedDuration,
            'actual_duration' => $actualDuration,
            'progress_percent' => $this->faker->numberBetween(0, 100),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'pending_review', 'completed']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'pending_payment', 'paid']),
            'owner_review' => $this->faker->optional(0.3)->paragraph(),
            'owner_rating' => $this->faker->optional(0.4)->numberBetween(1, 5),
            'is_approved' => $this->faker->boolean(30),
            'approved_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
            'approved_by' => $this->faker->optional(0.3)->passthrough(
                User::where('user_type', 'owner')->first()?->id ?? User::factory()->create(['user_type' => 'owner'])->id
            ),
        ];
    }

    // Sửa phương thức để đảm bảo ngày hợp lệ
    public function withValidDates(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', '+1 month');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            
            // Đảm bảo end_date sau start_date
            $endDate = (clone $startDate)->modify("+{$plannedDuration} days");
            
            return [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planned_duration' => $plannedDuration,
            ];
        });
    }

    // State methods - đảm bảo dates hợp lệ
    public function planned(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('+1 week', '+1 month');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            $endDate = (clone $startDate)->modify("+{$plannedDuration} days");
            
            return [
                'status' => 'planned',
                'progress_percent' => 0,
                'payment_status' => 'unpaid',
                'is_approved' => false,
                'approved_at' => null,
                'approved_by' => null,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planned_duration' => $plannedDuration,
                'actual_duration' => null,
            ];
        });
    }

    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-1 month', 'now');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            $endDate = (clone $startDate)->modify("+{$plannedDuration} days");
            
            return [
                'status' => 'in_progress',
                'progress_percent' => $this->faker->numberBetween(1, 99),
                'payment_status' => 'unpaid',
                'is_approved' => false,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planned_duration' => $plannedDuration,
                'actual_duration' => $this->faker->optional()->numberBetween(1, $plannedDuration),
            ];
        });
    }

    public function pendingReview(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-1 month', '-1 week');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            $endDate = now();
            
            return [
                'status' => 'pending_review',
                'progress_percent' => 100,
                'payment_status' => 'unpaid',
                'is_approved' => false,
                'end_date' => $endDate,
                'start_date' => $startDate,
                'planned_duration' => $plannedDuration,
                'actual_duration' => $plannedDuration,
            ];
        });
    }

    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', '-1 month');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            $endDate = $this->faker->dateTimeBetween($startDate, '-1 week');
            
            return [
                'status' => 'completed',
                'progress_percent' => 100,
                'payment_status' => $this->faker->randomElement(['pending_payment', 'paid']),
                'is_approved' => true,
                'owner_review' => $this->faker->paragraph(),
                'owner_rating' => $this->faker->numberBetween(3, 5),
                'approved_at' => now(),
                'approved_by' => User::where('user_type', 'owner')->first()?->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planned_duration' => $plannedDuration,
                'actual_duration' => $this->faker->numberBetween($plannedDuration - 2, $plannedDuration + 5),
            ];
        });
    }

    public function withBudget(int $min = 100000, int $max = 5000000): static
    {
        return $this->state(fn (array $attributes) => [
            'task_budget' => $this->faker->numberBetween($min, $max),
        ]);
    }

    public function withParent(): static
    {
        return $this->state(function (array $attributes) {
            $parentTask = Task::factory()->create();
            
            // Đảm bảo subtask có start_date sau hoặc bằng parent task
            $startDate = $this->faker->dateTimeBetween(
                $parentTask->start_date,
                $parentTask->end_date ?: '+1 month'
            );
            
            return [
                'parent_id' => $parentTask->id,
                'site_id' => $parentTask->site_id,
                'start_date' => $startDate,
                'task_budget' => $parentTask->task_budget * 0.3,
            ];
        });
    }

    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = $this->faker->dateTimeBetween('-2 months', '-1 month');
            $plannedDuration = $this->faker->numberBetween(5, 30);
            $endDate = $this->faker->dateTimeBetween($startDate, '-1 week');
            
            return [
                'is_approved' => true,
                'status' => 'completed',
                'progress_percent' => 100,
                'approved_at' => now(),
                'approved_by' => User::where('user_type', 'owner')->first()?->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'planned_duration' => $plannedDuration,
                'actual_duration' => $plannedDuration,
            ];
        });
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'is_approved' => true,
        ]);
    }
}