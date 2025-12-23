<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Task;
use App\Models\Site;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $paymentType = $this->faker->randomElement(['advance', 'milestone', 'final', 'retention']);
        $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']);
        
        // 1. Ưu tiên lấy Contract đã có, nếu không có mới tạo mới
        $contract = Contract::inRandomOrder()->first() ?? Contract::factory()->create();
        
        // 2. Lấy Project từ Contract đó
        $project = $contract->project;

        // 3. Lấy Site thuộc Project đó (tránh tạo Site lung tung)
        $site = Site::where('project_id', $project->id)->inRandomOrder()->first() 
                ?? Site::factory()->create(['project_id' => $project->id]);

        // 4. Lấy Task thuộc Site đó
        $task = Task::where('site_id', $site->id)->inRandomOrder()->first()
                ?? Task::factory()->create(['site_id' => $site->id]);
        
        $amount = match($paymentType) {
            'advance' => $contract->advance_payment,
            'milestone' => $task->task_budget ?? $this->faker->numberBetween(1000000, 5000000),
            default => $this->faker->numberBetween(1000000, 10000000),
        };
        
        return [
            'contract_id' => $contract->id,
            'task_id' => $paymentType === 'milestone' ? $task->id : null,
            'site_id' => $site->id,
            'project_id' => $project->id,
            'amount' => $amount,
            'pay_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'method' => $this->faker->randomElement(['bank_transfer', 'credit_card', 'cash']),
            'transaction_code' => $paymentType === 'cash' ? null : 'TXN-' . $this->faker->unique()->numberBetween(100000, 999999),
            'payment_type' => $paymentType,
            'status' => $status,
            'note' => $this->faker->optional(0.7)->sentence(),
            'receipt_file_path' => $this->faker->optional(0.5)->filePath(),
            'receipt_file_name' => $this->faker->optional(0.5)->word() . '.pdf',
            'created_by' => User::where('user_type', 'contractor')->inRandomOrder()->first()?->id ?? User::factory()->create(['user_type' => 'contractor'])->id,
            'approved_by' => $status === 'completed' ? (User::where('user_type', 'owner')->inRandomOrder()->first()?->id ?? User::factory()->create(['user_type' => 'owner'])->id) : null,
            'approved_at' => $status === 'completed' ? $this->faker->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
    // State methods
    public function advancePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'advance',
            'amount' => Contract::find($attributes['contract_id'])->advance_payment,
            'note' => 'Thanh toán tạm ứng hợp đồng',
        ]);
    }

    public function milestonePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'milestone',
            'note' => 'Thanh toán theo mốc công việc',
        ]);
    }

    public function finalPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'final',
            'note' => 'Thanh toán hoàn thành',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'approved_by' => User::where('user_type', 'owner')->first()?->id,
            'approved_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'note' => 'Giao dịch thất bại',
        ]);
    }

    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'bank_transfer',
            'transaction_code' => 'BANK-' . $this->faker->unique()->numberBetween(100000, 999999),
        ]);
    }

    public function cash(): static
    {
        return $this->state(fn (array $attributes) => [
            'method' => 'cash',
            'transaction_code' => null,
        ]);
    }
}