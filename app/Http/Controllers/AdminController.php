<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RoleChangeRequest;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function roleChangeRequests()
    {
        // Lấy user có request đang pending
        $users = User::whereHas('roleChangeRequests', function($q) {
            $q->where('status', 'pending');
        })->with(['roleChangeRequests' => function($q) {
            $q->where('status', 'pending');
        }])->get();
    
        return view('admin.role-change-requests', compact('users'));
    }
    
    public function processRoleChangeRequest(Request $request, $userId)
    {
        $request->validate([
            'request_id' => 'required|exists:role_change_requests,id',
            'action' => 'required|in:approve,reject',
            'admin_notes' => 'nullable|string|max:500'
        ]);
    
        $roleRequest = RoleChangeRequest::findOrFail($request->request_id);
        
        // Validation phụ: đảm bảo request thuộc về user
        if ($roleRequest->user_id != $userId) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu không khớp'], 400);
        }
    
        $status = $request->action === 'approve' ? 'approved' : 'rejected';
    
        $roleRequest->update([
            'status' => $status,
            'admin_notes' => $request->admin_notes,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);
    
        if ($status === 'approved') {
            // Cập nhật role cho user
            User::where('id', $userId)->update(['user_type' => $roleRequest->requested_role]);
        }
    
        return response()->json([
            'success' => true, 
            'message' => 'Đã xử lý yêu cầu thành công!'
        ]);
    }
}