<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    /**
     * Hiển thị danh sách các thanh toán của client
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Eager load đúng quan hệ
        $query = Payment::with(['contract.project.owner', 'contract.project.contractor']);
        
        // Lọc quyền xem thông qua bảng projects
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
        
        // Tìm kiếm theo mã hợp đồng và ghi chú
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('contract', function($q2) use ($search) {
                    $q2->where('contract_number', 'like', "%{$search}%")
                       ->orWhere('contract_name', 'like', "%{$search}%");
                })
                ->orWhere('reference_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        // Lọc theo phương thức thanh toán
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }
        
        // Lọc theo hợp đồng cụ thể
        if ($request->filled('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }
        
        // Lọc theo tháng (format: YYYY-MM)
        if ($request->filled('month')) {
            // Chuyển đổi từ format YYYY-MM thành phạm vi ngày
            $month = $request->month;
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
            
            $query->whereDate('pay_date', '>=', $startDate)
                  ->whereDate('pay_date', '<=', $endDate);
        }
        
        // Lọc theo khoảng thời gian (giữ lại cho tương thích)
        if ($request->filled('date_from')) {
            $query->whereDate('pay_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('pay_date', '<=', $request->date_to);
        }
        
        // Sắp xếp
        $query->orderBy('pay_date', 'desc')->orderBy('created_at', 'desc');
        
        $payments = $query->paginate(15)->withQueryString();
        
        // Lấy danh sách hợp đồng để filter
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
        
        // Thống kê với các điều kiện filter hiện tại
        $statsQuery = Payment::whereHas('contract.project', function($q) use ($user) {
            if ($user->user_type === 'owner') {
                $q->where('owner_id', $user->id);
            } elseif ($user->user_type === 'contractor') {
                $q->where('contractor_id', $user->id);
            }
        });
        
        // Áp dụng các filter tương tự cho thống kê
        if ($request->filled('search')) {
            $search = $request->search;
            $statsQuery->where(function($q) use ($search) {
                $q->whereHas('contract', function($q2) use ($search) {
                    $q2->where('contract_number', 'like', "%{$search}%")
                       ->orWhere('contract_name', 'like', "%{$search}%");
                })
                ->orWhere('reference_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('method')) {
            $statsQuery->where('method', $request->method);
        }
        
        if ($request->filled('contract_id')) {
            $statsQuery->where('contract_id', $request->contract_id);
        }
        
        if ($request->filled('month')) {
            $month = $request->month;
            $startDate = date('Y-m-01', strtotime($month));
            $endDate = date('Y-m-t', strtotime($month));
            
            $statsQuery->whereDate('pay_date', '>=', $startDate)
                      ->whereDate('pay_date', '<=', $endDate);
        }
        
        $allFilteredPayments = $statsQuery->get();
        
        // Thống kê
        $stats = [
            'total_amount' => $allFilteredPayments->sum('amount'),
            'total_count' => $allFilteredPayments->count(),
            'cash_amount' => $allFilteredPayments->where('method', 'cash')->sum('amount'),
            'bank_amount' => $allFilteredPayments->where('method', 'bank_transfer')->sum('amount'),
        ];
        
        return view('client.payments.index', compact('payments', 'contracts', 'stats'));
    }
    
    
    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);
        
        // Load quan hệ qua project
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

        // Kiểm tra file có tồn tại không
        if (!Storage::disk('public')->exists($payment->receipt_file_path)) {
            return back()->with('error', 'File biên lai không tồn tại trên hệ thống.');
        }

        // Tải file xuống
        $fileName = $payment->receipt_file_name ?: 'receipt_' . $payment->id . '.' . pathinfo($payment->receipt_file_path, PATHINFO_EXTENSION);
        
        return Storage::disk('public')->download($payment->receipt_file_path, $fileName);
    }

    /**
     * Xem biên lai trực tiếp
     */
    public function viewReceipt(Payment $payment)
    {
        $this->authorizePaymentAccess($payment);
        
        if (!$payment->receipt_file_path) {
            abort(404, 'Không tìm thấy file biên lai.');
        }

        if (!Storage::disk('public')->exists($payment->receipt_file_path)) {
            abort(404, 'File biên lai không tồn tại.');
        }

        // Trả về file để xem trực tiếp
        return response()->file(storage_path('app/public/' . $payment->receipt_file_path));
    }
    
    /**
     * Hiển thị form tạo thanh toán
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        // 1. Lấy danh sách hợp đồng để hiển thị trong select (như bạn đang làm)
        $contracts = Contract::whereHas('project', function($q) use ($user) {
                if ($user->user_type === 'owner') {
                    $q->where('owner_id', $user->id);
                } elseif ($user->user_type === 'contractor') {
                    $q->where('contractor_id', $user->id);
                }
            })
            ->whereIn('status', ['pending', 'active', 'processing'])
            ->get();

        // 2. Lấy ID từ URL (nếu có) để chọn sẵn
        $selected_contract_id = $request->query('contract_id');

        return view('client.payments.create', compact('contracts', 'selected_contract_id'));
    }

    /**
     * Lưu thanh toán mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'method' => 'required|in:cash,bank_transfer,credit_card,other',
            'receipt_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120', // Tăng lên 5MB
            'reference_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data = $request->except(['receipt_image', '_token']);
        $data['created_by'] = Auth::id();
        $data['status'] = 'completed'; // Mặc định là đã hoàn thành

        // Xử lý upload ảnh biên lai
        if ($request->hasFile('receipt_image')) {
            $file = $request->file('receipt_image');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Tạo tên file duy nhất
            $fileName = 'receipt_' . time() . '_' . Str::random(10) . '.' . $extension;
            
            // Lưu file vào thư mục payments/receipts
            $path = $file->storeAs('payments/receipts', $fileName, 'public');
            
            $data['receipt_file_path'] = $path;
            $data['receipt_file_name'] = $originalName;
        }

        // Tạo thanh toán
        $payment = Payment::create($data);

        // Cập nhật trạng thái hợp đồng nếu cần
        $contract = Contract::find($request->contract_id);
        if ($contract->status === 'pending') {
            $contract->status = 'active';
            $contract->save();
        }

        return redirect()->route('client.payments.show', $payment)
                        ->with('success', 'Đã tạo thanh toán thành công!');
    }
    
    /**
     * Kiểm tra quyền truy cập thanh toán
     */
    private function authorizePaymentAccess(Payment $payment)
    {
        $user = Auth::user();
        
        // Check quyền qua project
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