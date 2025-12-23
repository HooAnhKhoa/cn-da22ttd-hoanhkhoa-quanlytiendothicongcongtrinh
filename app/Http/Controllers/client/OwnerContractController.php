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
        // 1. Kiểm tra bảo mật: Chỉ Chủ đầu tư của hợp đồng này mới được duyệt
        if ($contract->owner_id !== Auth::id()) {
            return back()->with('error', 'Bạn không có quyền phê duyệt hợp đồng này.');
        }

        try {
            // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu
            DB::transaction(function () use ($contract) {
                // 2. Cập nhật trạng thái Hợp đồng sang 'active' (đã ký kết)
                $contract->update(['status' => 'active']);

                // 3. Cập nhật trạng thái Dự án liên quan sang 'in_progress' (Đang thi công)
                if ($contract->project) {
                    // Trạng thái 'in_progress' tương ứng với 'Đang thi công' trong model Project
                    $contract->project->update(['status' => 'in_progress']);
                }

                // 4. Ghi lại lịch sử phê duyệt vào bảng contract_approvals
                ContractApproval::create([
                    'contract_id' => $contract->id,
                    'approver_id' => Auth::id(),
                    'status'      => 'approved',
                    'approved_at' => now(),
                    'comments'    => 'Hợp đồng đã được phê duyệt qua hệ thống bởi Chủ đầu tư.'
                ]);

                // 5. Tự động tạo bản ghi thanh toán nếu hợp đồng có số tiền tạm ứng (advance_payment)
                if ($contract->advance_payment > 0) {
                    Payment::create([
                        'contract_id'  => $contract->id,
                        'project_id'   => $contract->project_id,
                        'amount'       => $contract->advance_payment,
                        'pay_date'     => now(),
                        'payment_type' => 'advance', // Loại: Tạm ứng
                        'status'       => 'pending', // Trạng thái: Chờ thanh toán thực tế
                        'created_by'   => Auth::id(),
                        'note'         => "Thanh toán tạm ứng tự động khi phê duyệt hợp đồng " . $contract->contract_number
                    ]);
                }
            });

            return back()->with('success', 'Hợp đồng đã được phê duyệt thành công. Dự án đã chuyển sang trạng thái thi công.');

        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra trong quá trình phê duyệt: ' . $e->getMessage());
        }
    }
}