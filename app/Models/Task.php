<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'parent_id',
        'task_name',
        'description',
        'start_date',
        'end_date',
        'planned_duration',
        'actual_duration',
        'progress_percent',
        'status'
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
}