<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Hiển thị danh sách các thanh toán của client
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // SỬA 1: Eager load đúng quan hệ (qua project)
        $query = Payment::with(['contract.project.owner', 'contract.project.contractor']);
        
        // SỬA 2: Lọc quyền xem thông qua bảng projects (vì contracts không còn owner_id)
        $query->whereHas('contract.project', function($q) use ($user) {
            if ($user->user_type === 'owner') {
                $q->where('owner_id', $user->id);
            } elseif ($user->user_type === 'contractor') {
                $q->where('contractor_id', $user->id);
            } else {
                $q->where(function($sub) use ($user) {
                    $sub->where('owner_id', $user->id)
                        ->orWhere('contractor_id', $user->id);
                });
            }
        });
        
        // Tìm kiếm theo mã hợp đồng
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('contract', function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('contract_name', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo phương thức thanh toán
        if ($request->has('method') && $request->method) {
            $query->where('method', $request->method);
        }
        
        // Lọc theo hợp đồng cụ thể
        if ($request->has('contract_id') && $request->contract_id) {
            $query->where('contract_id', $request->contract_id);
        }
        
        // Lọc theo khoảng thời gian
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('pay_date', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('pay_date', '<=', $request->date_to);
        }
        
        // Sắp xếp
        $query->orderBy('pay_date', 'desc')->orderBy('created_at', 'desc');
        
        $payments = $query->paginate(15)->withQueryString();
        
        // SỬA 3: Lấy danh sách hợp đồng để filter (cũng phải qua project)
        $contracts = Contract::whereHas('project', function($q) use ($user) {
                if ($user->user_type === 'owner') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->user_type === 'contractor') {
                    $q->where('contractor_id', $user->id);
                }
            })
            ->whereIn('status', ['active', 'completed'])
            ->orderBy('contract_number')
            ->get();
        
        // Thống kê (Lưu ý: paginate trả về LengthAwarePaginator, muốn sum phải dùng query gốc hoặc sum trên collection trang hiện tại)
        // Cách tốt nhất là clone query để sum toàn bộ (không chỉ trang hiện tại)
        // Tuy nhiên để đơn giản và giống logic cũ của bạn (sum trang hiện tại):
        $stats = [
            'total_amount' => $payments->sum('amount'),
            'total_count' => $payments->total(),
            'cash_amount' => $payments->where('method', 'cash')->sum('amount'),
            'bank_amount' => $payments->where('method', 'bank_transfer')->sum('amount'),
        ];
        
        return view('client.payments.index', compact('payments', 'contracts', 'stats'));
    }
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);
        
        // SỬA 4: Load quan hệ qua project
        $payment->load(['contract.project.owner', 'contract.project.contractor']);
        
        return view('client.payments.show', compact('payment'));
    }
    
    /**
     * Tải xuống biên lai
     */
    public function downloadReceipt(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);
        
        if (!$payment->receipt_file_path) {
            return back()->with('error', 'Không tìm thấy file biên lai.');
        }

        // Logic tải file
        // return Storage::download($payment->receipt_file_path, $payment->receipt_file_name);
        
        return back()->with('info', 'Chức năng đang phát triển');
    }
    
    /**
     * Kiểm tra quyền truy cập thanh toán
     */
    private function authorizePaymentAccess(Payment $payment)
    {
        $user = Auth::user();
        
        // SỬA 5: Check quyền qua project (vì contracts không có owner_id)
        $project = $payment->contract->project;

        if ($user->user_type === 'owner') {
            if ($project->owner_id !== $user->id) {
                abort(403, 'Bạn không có quyền xem thanh toán này');
            }
        } elseif ($user->user_type === 'contractor') {
            if ($project->contractor_id !== $user->id) {
                abort(403, 'Bạn không có quyền xem thanh toán này');
            }
        }
    }
}