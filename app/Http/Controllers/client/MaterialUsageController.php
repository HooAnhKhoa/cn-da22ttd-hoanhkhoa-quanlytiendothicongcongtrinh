<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\MaterialUsage;

class MaterialUsageController extends Controller
{
    public function create(Request $request)
    {
        $materialId = $request->get('material_id');
        $taskId = $request->get('task_id');
        
        // Lấy thông tin vật tư nếu có
        $material = null;
        if ($materialId) {
            $material = Material::find($materialId);
        }
        
        // Lấy thông tin công việc nếu có
        $task = null;
        if ($taskId) {
            $task = Task::find($taskId);
        } 
        
        // Lấy tất cả công việc để chọn
        $tasks = Task::all();
        $materials = Material::all();
        
        return view('client.material_usage.create', compact('material', 'task', 'tasks', 'materials'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'task_id' => 'required|exists:tasks,id',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        // Kiểm tra xem đã tồn tại chưa
        $existing = MaterialUsage::where('task_id', $validated['task_id'])
            ->where('material_id', $validated['material_id'])
            ->first();
            
        if ($existing) {
            return back()->withErrors(['material_id' => 'Vật tư này đã được thêm vào công việc.']);
        }
        
        MaterialUsage::create($validated);
        
        return redirect()->route('client.tasks.show', $validated['task_id'])
            ->with('success', 'Vật tư đã được thêm vào công việc!');
    }
    
    public function edit(MaterialUsage $materialUsage)
    {
        $materials = Material::all();
        return view('material_usage.edit', compact('materialUsage', 'materials'));
    }
    
    public function update(Request $request, MaterialUsage $materialUsage)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'usage_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);
        
        $materialUsage->update($validated);
        
        return redirect()->route('tasks.show', $materialUsage->task_id)
            ->with('success', 'Thông tin vật tư đã được cập nhật!');
    }
    
    public function destroy(MaterialUsage $materialUsage)
    {
        $taskId = $materialUsage->task_id;
        $materialUsage->delete();
        
        return redirect()->route('tasks.show', $taskId)
            ->with('success', 'Vật tư đã được xóa khỏi công việc!');
    }
    
    public function exportReport(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $materialId = $request->get('material_id');
        
        $query = MaterialUsage::with(['task', 'material'])
            ->orderBy('usage_date', 'desc');
            
        if ($startDate && $endDate) {
            $query->whereBetween('usage_date', [$startDate, $endDate]);
        }
        
        if ($materialId) {
            $query->where('material_id', $materialId);
        }
        
        $usageData = $query->get();
        
        return view('material_usage.report', compact('usageData', 'startDate', 'endDate'));
    }
}