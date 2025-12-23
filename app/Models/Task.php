<?php

namespace App\Models;

use App\Models\ProgressUpdate;
use App\Models\MaterialUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'parent_id',
        'assigned_engineer_id',
        'task_code',
        'task_name',
        'description',
        'task_budget',
        'start_date',
        'end_date',
        'planned_duration',
        'actual_duration',
        'progress_percent',
        'status',
        'payment_status',
        'owner_review',
        'owner_rating',
        'is_approved',
        'approved_at',
        'approved_by'
    ];

    // Định dạng ngày tháng
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Task::class, 'parent_id');
    }

    public function progressUpdates()
    {
        return $this->hasMany(ProgressUpdate::class)->orderBy('date', 'desc');
    }

    public function delays()
    {
        return $this->hasMany(Delay::class);
    }

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }

    public function equipmentUsages()
    {
        return $this->hasMany(EquipmentUsage::class);
    }

    public function latestProgressUpdate()
    {
        return $this->hasOne(ProgressUpdate::class)->latestOfMany();
    }

    public function inspections()
    {
        return $this->hasMany(Inspection::class);
    }

    public function issues()
    {
        return $this->hasMany(Issue::class);
    }

    public function progressReports()
    {
        return $this->hasMany(ProgressUpdate::class)->orderBy('date', 'desc');
    }

    public function latestProgressReport()
    {
        return $this->hasOne(ProgressUpdate::class)->latestOfMany();
    }

    public function isStatusCorrect(): bool
    {
        $progress = (int) $this->progress_percent;
        $expectedStatus = '';
        
        if ($progress === 0) {
            $expectedStatus = 'planned';
        } elseif ($progress > 0 && $progress < 100) {
            $expectedStatus = 'in_progress';
        } elseif ($progress === 100) {
            $expectedStatus = 'completed';
        }
        
        return $this->status === $expectedStatus;
    }

    /**
     * Lấy trạng thái tính toán từ tiến độ
     */
    public function getCalculatedStatusAttribute(): string
    {
        $progress = (int) $this->progress_percent;
        
        if ($progress === 0) {
            return 'planned';
        } elseif ($progress > 0 && $progress < 100) {
            return 'in_progress';
        } elseif ($progress === 100) {
            return 'completed';
        }
        
        return $this->status;
    }
}