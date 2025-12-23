<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Contract::with(['project', 'owner'])
            ->where('contractor_id', $user->id); // Luôn lọc theo ID người dùng

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                  ->orWhere('contract_name', 'like', "%{$search}%");
            });
        }

        $contracts = $query->paginate(10);

        return view('client.contracts.index', compact('contracts'));
    }

    public function show(Contract $contract)
    {
        // Bảo mật: Nếu không phải chủ hợp đồng thì không cho xem
        if (auth()->id() !== $contract->contractor_id) {
            abort(403);
        }

        $contract->load(['project', 'owner', 'payments', 'approvals.approver']);
        
        $totalPaid = $contract->total_paid;
        $remaining = $contract->remaining_amount;
        $progress = $contract->contract_value > 0 ? ($totalPaid / $contract->contract_value) * 100 : 0;

        return view('client.contracts.show', compact('contract', 'totalPaid', 'remaining', 'progress'));
    }
}