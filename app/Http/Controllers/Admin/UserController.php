<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Danh sách người dùng
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Tìm kiếm
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Lọc theo vai trò
        if ($request->filled('role')) {
            $query->where('user_type', $request->role);
        }

        $users = $query->latest()->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Form tạo mới
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:admin,owner,contractor,engineer',
            'status' => 'required|in:active,inactive,locked',
        ]);

        // Mã hóa mật khẩu
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Tạo người dùng mới thành công.');
    }

    /**
     * Form chỉnh sửa
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật người dùng
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'user_type' => 'required|in:admin,owner,contractor,engineer',
            'status' => 'required|in:active,inactive,locked',
            'password' => 'nullable|string|min:8|confirmed', // Mật khẩu không bắt buộc
        ]);

        // Xử lý mật khẩu: Chỉ cập nhật nếu người dùng nhập vào
        if (filled($request->password)) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Cập nhật thông tin người dùng thành công.');
    }

    /**
     * Xóa người dùng
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể tự xóa chính mình!');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Đã xóa người dùng thành công.');
    }
    public function show(User $user)
    {
        // Load các quan hệ để hiển thị thống kê (nếu cần)
        $user->loadCount(['ownedProjects', 'contractedProjects', 'engineeredProjects']);
        
        return view('admin.users.show', compact('user'));
    }
    // app/Http/Controllers/Admin/UserController.php

    public function updateStatus(Request $request, User $user)
    {
        // Ngăn chặn tự khóa chính mình
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Bạn không thể thay đổi trạng thái của chính mình.');
        }

        $request->validate([
            'status' => 'required|in:active,inactive,locked'
        ]);

        $user->update(['status' => $request->status]);

        return back()->with('success', "Đã cập nhật trạng thái của {$user->username} thành " . ucfirst($request->status));
    }

    /**
     * Chấp nhận yêu cầu đổi vai trò
     */
    public function approveRoleRequest(Request $request, User $user, $requestId)
    {
        // Tìm request trong bảng RoleChangeRequest
        $roleRequest = RoleChangeRequest::findOrFail($requestId);

        // Kiểm tra logic
        if ($roleRequest->user_id != $user->id) {
            return back()->with('error', 'Yêu cầu không khớp với người dùng này.');
        }
        if ($roleRequest->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        // 1. Cập nhật trạng thái yêu cầu
        $roleRequest->update([
            'status' => 'approved',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'admin_notes' => 'Được duyệt nhanh từ trang chi tiết người dùng.'
        ]);

        // 2. Cập nhật User Type cho người dùng
        $user->update(['user_type' => $roleRequest->requested_role]);

        return back()->with('success', 'Đã duyệt yêu cầu và cập nhật vai trò người dùng.');
    }

    /**
     * Từ chối yêu cầu đổi vai trò
     */
    public function rejectRoleRequest(Request $request, User $user, $requestId)
    {
        $roleRequest = RoleChangeRequest::findOrFail($requestId);

        if ($roleRequest->user_id != $user->id) {
            return back()->with('error', 'Yêu cầu không hợp lệ.');
        }

        $roleRequest->update([
            'status' => 'rejected',
            'processed_by' => auth()->id(),
            'processed_at' => now(),
            'admin_notes' => 'Đã từ chối từ trang quản lý người dùng.'
        ]);

        return back()->with('success', 'Đã từ chối yêu cầu đổi vai trò.');
    }
}