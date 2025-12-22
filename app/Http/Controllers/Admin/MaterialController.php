<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Material;

class MaterialController extends Controller
{
    // Hiển thị danh sách vật tư
    public function index(Request $request)
    {
        $query = Material::query();

        // 🔍 Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('materials_name', 'like', "%{$search}%")
                ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        // 🏷️ Filter theo loại vật tư
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // 📦 Filter theo đơn vị
        if ($request->filled('unit')) {
            $query->where('unit', $request->unit);
        }

        // 📄 Phân trang
        $materials = $query
            ->paginate(10)
            ->withQueryString();

        return view('admin.materials.index', compact('materials'));
    }

    // Hiển thị form tạo vật tư
    public function create()
    {
        $types = Material::getTypes();
        $units = Material::getUnits();
        
        return view('admin.materials.create', compact('types', 'units'));
    }

    // Lưu vật tư mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
        ]);

        Material::create($validated);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Vật tư đã được tạo thành công!');
    }

    // Hiển thị chi tiết vật tư
    public function show(Material $material)
    {
        $usageHistory = $material->usages()
            ->with(['task.site'])
            ->orderBy('usage_date', 'desc')
            ->paginate(10);

        return view('admin.materials.show', compact('material', 'usageHistory'));
    }


    // Hiển thị form chỉnh sửa
    public function edit(Material $material)
    {
        $types = Material::getTypes();
        $units = Material::getUnits();
        
        return view('admin.materials.edit', compact('material', 'types', 'units'));
    }

    // Cập nhật vật tư
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'materials_name' => 'required|string|max:255',
            'unit' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'supplier' => 'required|string|max:255',
        ]);

        $material->update($validated);

        return redirect()->route('admin.materials.show', $material)
            ->with('success', 'Vật tư đã được cập nhật thành công!');
    }

    // Xóa vật tư
    public function destroy(Material $material)
    {
        $material->delete();
        
        return redirect()->route('admin.materials.index')
            ->with('success', 'Vật tư đã được xóa thành công!');
    }

    // API: Lấy vật tư theo loại
    public function getByType(Request $request)
    {
        $type = $request->get('type');
        
        $materials = Material::where('type', $type)->get();
        
        return response()->json($materials);
    }

    // Thống kê vật tư
    public function statistics()
    {
        $totalMaterials = Material::count();
        $byType = Material::groupBy('type')
            ->selectRaw('type, count(*) as count')
            ->get();
        $bySupplier = Material::groupBy('supplier')
            ->selectRaw('supplier, count(*) as count')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();
            
        return view('materials.statistics', compact('totalMaterials', 'byType', 'bySupplier'));
    }
}