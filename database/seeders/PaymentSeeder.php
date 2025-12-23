<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Contract;
use App\Models\Task;
use App\Models\Site;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo thanh toán cho hợp đồng
        $contracts = Contract::with('project.sites')->get();
        
        foreach ($contracts as $contract) {
            // Lấy 1 site bất kỳ thuộc dự án của hợp đồng này
            $site = $contract->project->sites->first() ?? Site::factory()->create(['project_id' => $contract->project_id]);

            $paymentCount = rand(1, 4);
            
            for ($i = 0; $i < $paymentCount; $i++) {
                $paymentType = $i === 0 ? 'advance' : fake()->randomElement(['milestone', 'final']);

                // QUAN TRỌNG: Truyền đủ các ID để Factory không tự tạo Project/Contract mới
                Payment::factory()->create([
                    'contract_id' => $contract->id,
                    'project_id'  => $contract->project_id,
                    'site_id'     => $site->id, 
                    'payment_type' => $paymentType,
                    'amount'      => $this->getAmountForPayment($contract, $paymentType, $i),
                    'status'      => $contract->status === 'active' ? 'completed' : 'pending',
                ]);
            }
            
            // Cập nhật total_paid cho contract
            $totalPaid = $contract->payments()->where('status', 'completed')->sum('amount');
            $contract->total_paid = $totalPaid;
            $contract->save();
        }

        // 2. Tạo thanh toán cho các task đã approved
        // Sử dụng with để lấy site và project tránh N+1 query và lỗi logic
        $approvedTasks = Task::with('site')->where('is_approved', true)->get();
        
        foreach ($approvedTasks as $task) {
            if (in_array($task->payment_status, ['unpaid', 'pending_payment'])) {
                Payment::factory()->milestonePayment()->create([
                    'task_id'    => $task->id,
                    'site_id'    => $task->site_id,
                    'project_id' => $task->site->project_id,
                    'contract_id' => Contract::where('project_id', $task->site->project_id)->first()?->id 
                                     ?? Contract::factory()->create(['project_id' => $task->site->project_id])->id,
                    'amount'     => $task->task_budget,
                    'status'     => $task->payment_status === 'paid' ? 'completed' : 'pending',
                ]);
                
                if ($task->payment_status === 'paid') {
                    $task->site->increment('paid_amount', $task->task_budget);
                }
            }
        }

        $this->command->info('Đã tạo ' . Payment::count() . ' thanh toán.');
    }

    private function getAmountForPayment($contract, $paymentType, $index): float
    {
        return match($paymentType) {
            'advance' => $contract->advance_payment,
            'milestone' => ($contract->contract_value - $contract->advance_payment) * 0.3,
            'final' => ($contract->contract_value - $contract->advance_payment) * 0.4,
            default => $contract->contract_value * 0.1,
        };
    }
}