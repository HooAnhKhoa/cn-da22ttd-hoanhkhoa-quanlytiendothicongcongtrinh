<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function roleChangeRequests()
    {
        // Lấy tất cả user có yêu cầu đổi role đang chờ
        $users = User::whereNotNull('role_change_requests')
            ->get()
            ->filter(function ($user) {
                return $user->hasPendingRoleRequest();
            });

        return view('admin.role-change-requests', compact('users'));
    }

    public function processRoleChangeRequest(Request $request, $userId)
    {
        $request->validate([
            'request_id' => 'required',
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500'
        ]);

        $user = User::findOrFail($userId);
        
        // Xử lý yêu cầu
        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        
        $user->processRoleChangeRequest(
            $request->request_id,
            $status,
            $request->admin_notes,
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Yêu cầu đã được xử lý thành công!'
        ]);
    }
}