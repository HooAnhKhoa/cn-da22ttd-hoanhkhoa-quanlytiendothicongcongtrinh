<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\ContractApproval;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OwnerContractController extends Controller
{
    public function approve(Contract $contract)
    {
        // Load quan hệ project để lấy thông tin chủ đầu tư
        $contract->load('project');

        // 1. SỬA LỖI BẢO MẬT: Kiểm tra thông qua Project owner_id
        // Vì bảng contracts đã xóa cột owner_id
        if (!$contract->project || $contract->project->owner_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền phê duyệt hợp đồng này.');
        }

        try {
            DB::transaction(function () use ($contract) {
                // 2. Cập nhật trạng thái Hợp đồng
                $contract->update([
                    'status' => 'active',
                    'signed_date' => now(), // Cập nhật ngày ký là ngày duyệt (nếu cần)
                ]);

                // 3. Cập nhật trạng thái Dự án (nếu dự án đang chờ hợp đồng)
                if ($contract->project && $contract->project->status === 'pending_contract') {
                    $contract->project->update(['status' => 'in_progress']);
                }

                // 4. Ghi lịch sử phê duyệt (Cập nhật bản ghi pending thành approved)
                // Thay vì tạo mới luôn, ta nên check xem có bản ghi pending nào không để update
                $approval = ContractApproval::where('contract_id', $contract->id)
                    ->where('status', 'pending')
                    ->first();

                if ($approval) {
                    $approval->update([
                        'status'      => 'approved',
                        'approver_id' => Auth::id(),
                        'approved_at' => now(),
                        'reviewed_at' => now(), // Đánh dấu đã xem
                        'comments'    => 'Đã phê duyệt.'
                    ]);
                } else {
                    // Nếu chưa có thì tạo mới
                    ContractApproval::create([
                        'contract_id' => $contract->id,
                        'approver_id' => Auth::id(),
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'reviewed_at' => now(),
                        'comments'    => 'Phê duyệt trực tiếp bởi Chủ đầu tư.'
                    ]);
                }

                // 5. Tạo thanh toán tạm ứng (nếu có)
                if ($contract->advance_payment > 0) {
                    // Kiểm tra xem đã có thanh toán tạm ứng chưa để tránh trùng lặp
                    $exists = Payment::where('contract_id', $contract->id)
                        ->where('payment_type', 'advance')
                        ->exists();

                    if (!$exists) {
                        Payment::create([
                            'contract_id'  => $contract->id,
                            // Lưu ý: Nếu bạn đã xóa project_id trong bảng payments thì bỏ dòng dưới đi
                            // 'project_id'   => $contract->project_id, 
                            'amount'       => $contract->advance_payment,
                            'pay_date'     => now(),
                            'payment_type' => 'advance',
                            'status'       => 'pending',
                            'created_by'   => Auth::id(),
                            'method'       => 'bank_transfer', // Mặc định phương thức
                            'note'         => "Thanh toán tạm ứng hợp đồng " . $contract->contract_number
                        ]);
                    }
                }
            });
            $contract->update(['status' => 'active']);
            return back()->with('success', 'Hợp đồng đã được phê duyệt thành công.');

        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}