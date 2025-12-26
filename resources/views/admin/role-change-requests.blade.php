@extends('layouts.app')

@section('title', 'Quản lý yêu cầu đổi vai trò')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Quản lý yêu cầu đổi vai trò</h1>
    
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò hiện tại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò yêu cầu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lý do</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày gửi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    @foreach($user->getRoleChangeRequestsList() as $request)
                        @if($request['status'] == 'pending')
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->username }}</div>
                                    <div class="text-sm text-gray-500 ml-2">{{ $user->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ ucfirst($user->user_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($request['requested_role']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate">{{ $request['reason'] }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($request['created_at'])->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Đang chờ
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="processRequest('{{ $user->id }}', '{{ $request['id'] }}', 'approve')"
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    Duyệt
                                </button>
                                <button onclick="processRequest('{{ $user->id }}', '{{ $request['id'] }}', 'reject')"
                                        class="text-red-600 hover:text-red-900">
                                    Từ chối
                                </button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal nhập ghi chú -->
<div id="notesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Nhập ghi chú</h3>
        <textarea id="adminNotes" rows="3" class="w-full border rounded-lg p-2 mb-4" placeholder="Nhập ghi chú (nếu có)"></textarea>
        <div class="flex justify-end space-x-2">
            <button onclick="closeNotesModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Hủy</button>
            <button onclick="confirmProcessRequest()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Xác nhận</button>
        </div>
    </div>
</div>

<script>
let currentRequest = null;

function processRequest(userId, requestId, action) {
    currentRequest = { userId, requestId, action };
    document.getElementById('notesModal').classList.remove('hidden');
}

function closeNotesModal() {
    document.getElementById('notesModal').classList.add('hidden');
    currentRequest = null;
    document.getElementById('adminNotes').value = '';
}

function confirmProcessRequest() {
    const notes = document.getElementById('adminNotes').value;
    
    fetch(`/admin/role-change-requests/${currentRequest.userId}/process`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            request_id: currentRequest.requestId,
            action: currentRequest.action,
            admin_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    })
    .finally(() => {
        closeNotesModal();
    });
}
</script>
@endsection