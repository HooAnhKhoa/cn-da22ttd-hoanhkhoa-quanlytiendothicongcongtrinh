<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_name',
        'owner_id',
        'contractor_id', 
        'engineer_id',
        'location',
        'start_date',
        'end_date',
        'description',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function sites()
    {
        return $this->hasMany(Site::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public static function getStatuses()
    {
        return [
            'draft' => 'Bản nháp',
            'pending_contract' => 'Chờ hợp đồng',
            'in_progress' => 'Đang thi công',
            'completed' => 'Hoàn thành',
            'on_hold' => 'Tạm dừng',
            'cancelled' => 'Đã hủy'
        ];
    }

    // Tính tổng ngân sách từ các hợp đồng
    public function getTotalBudgetAttribute()
    {
        return $this->contracts()->sum('contract_value');
    }
}