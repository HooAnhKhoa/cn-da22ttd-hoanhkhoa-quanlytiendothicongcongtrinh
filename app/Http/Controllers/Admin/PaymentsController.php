<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['contract', 'contract.project'])
            ->orderBy('pay_date', 'desc');

        // Search by contract code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('contract', function($q) use ($search) {
                $q->where('contract_code', 'like', "%{$search}%");
            });
        }

        // Filter by payment method
        if ($request->has('method') && $request->method) {
            $query->where('method', $request->method);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('pay_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('pay_date', '<=', $request->date_to);
        }

        // Get statistics
        $payments = $query->paginate(15);
        
        $totalAmount = Payment::sum('amount');
        $totalCount = Payment::count();
        $cashCount = Payment::where('method', 'cash')->count();
        $bankCount = Payment::where('method', 'bank_transfer')->count();

        return view('admin.payments.index', compact('payments', 'totalAmount', 'totalCount', 'cashCount', 'bankCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $contracts = Contract::with('project')->orderBy('contract_code')->get();
        return view('admin.payments.create', compact('contracts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'method' => 'required|in:cash,bank_transfer,credit_card,other',
            'note' => 'nullable|string|max:500'
        ]);

        Payment::create($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Thanh toán đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['contract', 'contract.project', 'contract.customer']);
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        $contracts = Contract::with('project')->orderBy('contract_code')->get();
        return view('admin.payments.edit', compact('payment', 'contracts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required|numeric|min:0',
            'pay_date' => 'required|date',
            'method' => 'required|in:cash,bank_transfer,credit_card,other',
            'note' => 'nullable|string|max:500'
        ]);

        $payment->update($validated);

        return redirect()->route('admin.payments.index')
            ->with('success', 'Thanh toán đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Thanh toán đã được xóa thành công.');
    }

    /**
     * Get payment statistics
     */
    public function statistics()
    {
        // Monthly payments
        $monthlyPayments = Payment::select(
                DB::raw('DATE_FORMAT(pay_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('pay_date', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Payment methods distribution
        $methodDistribution = Payment::select(
                'method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('method')
            ->get();

        // Top contracts by payments
        $topContracts = Payment::with('contract')
            ->select('contract_id', DB::raw('SUM(amount) as total_paid'))
            ->groupBy('contract_id')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return view('admin.payments.statistics', compact(
            'monthlyPayments',
            'methodDistribution',
            'topContracts'
        ));
    }
}