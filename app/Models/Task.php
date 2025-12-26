<?php

namespace App\Models;

use App\Models\ProgressUpdate;
use App\Models\MaterialUsage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

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
        // Đã xóa planned_duration, actual_duration (tính toán từ date)
        'progress_percent',
        'status',
        // Đã xóa payment_status
        'owner_review',
        'owner_rating',
        'is_approved',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'task_budget' => 'decimal:2',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime'
    ];

    // Accessor tính toán Duration
    public function getPlannedDurationAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date);
        }
        return 0;
    }

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

    public function materialUsages(): HasMany
    {
        return $this->hasMany(MaterialUsage::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}