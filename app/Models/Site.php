<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'project_id',
        'site_code',
        'site_name',
        'description',
        'total_budget',
        'paid_amount',
        // 'remaining_budget',
        'start_date',
        'end_date',
        'progress_percent',
        'status',
        'payment_status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percent' => 'integer'
    ];

    // Quan hệ với Project
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }

    // KHÔNG có engineer(), contractor(), supervisor()
    public static function getStatuses(): array
    {
        return [
            'planned' => 'Đã lên kế hoạch',
            'in_progress' => 'Đang triển khai',
            'completed' => 'Đã hoàn thành',
            'on_hold' => 'Tạm dừng',
            'cancelled' => 'Đã hủy',    
        ];
    }
    
}