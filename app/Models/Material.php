<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{

    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'materials_name',
        'unit',
        'type',
        'supplier'
    ];

    protected $casts = [
        'quantity' => 'decimal:2'
    ];

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function usages()
    {
        return $this->hasMany(MaterialUsage::class);
    }

    // Quan hệ với Task qua bảng trung gian
     public function tasks()
    {
        return $this->belongsToMany(Task::class, 'material_usages')
                    ->using(MaterialUsage::class)
                    ->withPivot('quantity', 'usage_date');
    }

    // Scope tìm kiếm
    public function scopeSearch($query, $search)
    {
        return $query->where('materials_name', 'like', '%' . $search . '%')
                     ->orWhere('type', 'like', '%' . $search . '%')
                     ->orWhere('supplier', 'like', '%' . $search . '%');
    }

    // Lấy các loại vật tư có sẵn
    public static function getTypes(): array
    {
        return [
            'building_materials' => 'Vật liệu xây dựng',
            'electrical' => 'Thiết bị điện',
            'plumbing' => 'Thiết bị nước',
            'finishing' => 'Vật liệu hoàn thiện',
            'tools' => 'Công cụ, dụng cụ',
            'safety' => 'Thiết bị an toàn',
            'other' => 'Khác'
        ];
    }

    // Lấy các đơn vị tính
    public static function getUnits(): array
    {
        return [
            'kg' => 'Kilogram',
            'ton' => 'Tấn',
            'm' => 'Mét',
            'm2' => 'Mét vuông',
            'm3' => 'Mét khối',
            'piece' => 'Cái/Chiếc',
            'box' => 'Hộp/Thùng',
            'roll' => 'Cuộn',
            'bag' => 'Bao'
        ];
    }
}