<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $paymentType = $this->faker->randomElement(['advance', 'milestone', 'final', 'retention']);
        $status = $this->faker->randomElement(['pending', 'processing', 'completed', 'failed']);
        
        // Logic lấy quan hệ
        $contract = Contract::inRandomOrder()->first() ?? Contract::factory()->create();
        
        // Lấy task ngẫu nhiên (nếu có)
        $taskId = null;
        if ($paymentType === 'milestone') {
             $taskId = Task::inRandomOrder()->value('id');
        }
        
        // Tính số tiền giả định
        $amount = match($paymentType) {
            'advance' => $contract->advance_payment,
            'milestone' => $this->faker->numberBetween(1000000, 5000000),
            default => $this->faker->numberBetween(1000000, 10000000),
        };
        
        return [
            'contract_id' => $contract->id,
            'task_id' => $taskId,
            // Đã xóa site_id, project_id
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

    // --- CÁC PHƯƠNG THỨC STATE BỊ THIẾU ---

    public function milestonePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'milestone',
            'note' => 'Thanh toán theo mốc công việc (Milestone)',
        ]);
    }

    public function advancePayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'advance',
            'note' => 'Thanh toán tạm ứng hợp đồng',
        ]);
    }

    public function finalPayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'final',
            'note' => 'Thanh toán quyết toán/nghiệm thu',
        ]);
    }
}