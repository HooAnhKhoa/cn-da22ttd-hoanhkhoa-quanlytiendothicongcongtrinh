<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'project_id',
        'site_code',
        'site_name',
        'description',
        'total_budget',
        // Đã xóa paid_amount (tính toán động)
        'start_date',
        'end_date',
        'progress_percent',
        'status',
        // Đã xóa payment_status
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress_percent' => 'integer',
        'total_budget' => 'decimal:2'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function tasks() : HasMany
    {
        return $this->hasMany(Task::class);
    }

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
    
    // Tính toán số tiền đã chi cho Site thông qua Task -> Payment (nếu cần)
    // Hoặc nếu Payment gắn với Contract, thì Site chỉ quản lý tiến độ thi công.
}   