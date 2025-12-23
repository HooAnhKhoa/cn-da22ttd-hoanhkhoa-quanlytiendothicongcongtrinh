<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\ContractApproval;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContractApprovalSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy tất cả hợp đồng
        $contracts = Contract::all();
        
        // Lấy các owner (người có quyền phê duyệt)
        $owners = User::where('user_type', 'owner')->get();
        
        if ($owners->isEmpty()) {
            $this->command->info('Không có owner nào để tạo phê duyệt hợp đồng.');
            return;
        }

        foreach ($contracts as $contract) {
            // Mỗi hợp đồng tạo 1-2 phê duyệt
            $count = rand(1, 2);
            
            for ($i = 0; $i < $count; $i++) {
                ContractApproval::create([
                    'contract_id' => $contract->id,
                    'approver_id' => $owners->random()->id,
                    'status' => $this->getStatusForContract($contract),
                    'comments' => rand(0, 1) ? $this->getRandomComments() : null,
                    'reviewed_at' => $contract->status !== 'draft' ? now()->subDays(rand(1, 30)) : null,
                    'approved_at' => $contract->status === 'active' ? now()->subDays(rand(1, 30)) : null,
                    'rejected_at' => $contract->status === 'draft' && rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                ]);
            }
            
            // Nếu hợp đồng đang ở trạng thái pending_signature, tạo phê duyệt pending
            if ($contract->status === 'pending_signature') {
                ContractApproval::create([
                    'contract_id' => $contract->id,
                    'approver_id' => $owners->random()->id,
                    'status' => 'pending',
                    'comments' => 'Vui lòng xem xét và phê duyệt hợp đồng này.',
                ]);
            }
        }

        $this->command->info('Đã tạo ' . ContractApproval::count() . ' bản ghi phê duyệt hợp đồng.');
    }

    private function getStatusForContract(Contract $contract): string
    {
        return match($contract->status) {
            'active' => 'approved',
            'draft' => fake()->randomElement(['rejected', 'cancelled']),
            'pending_signature' => 'pending',
            default => fake()->randomElement(['pending', 'approved', 'rejected']),
        };
    }

    private function getRandomComments(): string
    {
        $comments = [
            'Hợp đồng đã được xem xét và đạt yêu cầu.',
            'Cần chỉnh sửa một số điều khoản về thanh toán.',
            'Đã phê duyệt, vui lòng tiến hành ký kết.',
            'Thiếu thông tin về bảo hành công trình.',
            'Hợp đồng đầy đủ và chi tiết, có thể phê duyệt.',
            'Cần bổ sung phụ lục về tiến độ thanh toán.',
            'Đã từ chối do giá trị hợp đồng vượt ngân sách.',
            'Phê duyệt có điều kiện: cần bổ sung biên bản nghiệm thu mẫu.',
        ];
        
        return $comments[array_rand($comments)];
    }
}