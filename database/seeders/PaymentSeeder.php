<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Task;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo thanh toán cho hợp đồng
        $contracts = Contract::all();
        
        foreach ($contracts as $contract) {
            $paymentCount = rand(1, 4);
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $paymentType = $i === 0 ? 'advance' : fake()->randomElement(['milestone', 'final']);

                Payment::factory()->create([
                    'contract_id' => $contract->id,
                    // Bỏ project_id, site_id vì đã xóa khỏi bảng payments
                    'task_id' => null,
                    'payment_type' => $paymentType,
                    'amount'      => $this->getAmountForPayment($contract, $paymentType),
                    'status'      => $contract->status === 'active' || $contract->status === 'completed' 
                                     ? fake()->randomElement(['completed', 'processing']) 
                                     : 'pending',
                ]);
            }
            
            // Đã xóa logic cập nhật contract->total_paid
        }

        // 2. Tạo thanh toán cho các task đã approved
        // (Lưu ý: Chỉ tạo payment, không check payment_status cũ nữa)
        $approvedTasks = Task::where('is_approved', true)->get();
        
        foreach ($approvedTasks as $task) {
            // Random: 70% task đã duyệt sẽ có thanh toán
            if (rand(1, 100) <= 70) {
                Payment::factory()->milestonePayment()->create([
                    'task_id'     => $task->id,
                    'contract_id' => null, // Thanh toán theo task không nhất thiết gắn contract
                    // Bỏ site_id, project_id
                    'amount'      => $task->task_budget,
                    'status'      => fake()->randomElement(['completed', 'processing', 'pending']),
                ]);
                
                // Đã xóa logic cập nhật site->paid_amount
            }
        }

        $this->command->info('Đã tạo ' . Payment::count() . ' thanh toán.');
    }

    private function getAmountForPayment($contract, $paymentType): float
    {
        return match($paymentType) {
            'advance' => $contract->advance_payment,
            'milestone' => ($contract->contract_value - $contract->advance_payment) * 0.3,
            'final' => ($contract->contract_value - $contract->advance_payment) * 0.4,
            default => $contract->contract_value * 0.1,
        };
    }
}