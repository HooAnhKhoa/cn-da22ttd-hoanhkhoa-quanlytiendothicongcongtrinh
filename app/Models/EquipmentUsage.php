<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipmentUsage extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'task_id',
        'equipment_id',
        'start_time',
        'end_time',
        'engineer_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }
}